<?php

namespace App\Command;

use App\Entity\Product;
use App\Model\ProductDTO;
use App\Services\Parsers\CsvParser;
use App\Utils\ProductFactory;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

class ImportProductsCommand extends Command
{
    protected static $defaultName = 'app:import-products';

    private const BATCH_AMOUNT = 1000;

    /**
     * @var CsvParser
     */
    protected $parser;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param CsvParser              $parser
     * @param EntityManagerInterface $em
     * @param Filesystem             $filesystem
     * @param ValidatorInterface     $validator
     * @param LoggerInterface        $logger
     */
    public function __construct(CsvParser $parser, EntityManagerInterface $em, Filesystem $filesystem, ValidatorInterface $validator, LoggerInterface $logger)
    {
        $this->parser = $parser;
        $this->em = $em;
        $this->filesystem = $filesystem;
        $this->validator = $validator;
        $this->logger = $logger;

        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Import products from CSV file')
            ->setHelp('This command import products from CSV file')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to file')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $created = 0;
        $updated = 0;
        $errors = 0;

        $path = $input->getArgument('path');
        if (!$this->filesystem->exists($path)) {
            $this->logger->error('File not found.');

            return 0;
        }

        try {
            foreach ($this->parser->parse($path) as $productDTO) {
                if (!$this->isValid($productDTO)) {
                    ++$errors;

                    continue;
                }

                $existed = $this->em
                    ->getRepository(Product::class)
                    ->findOneBySku($productDTO->sku)
                ;

                if ($existed) {
                    if ($this->isUpdated($existed, $productDTO)) {
                        ++$updated;
                    }

                    continue;
                }

                $this->em->persist(ProductFactory::fromProductDTO($productDTO));

                ++$created;

                if (0 === $updated + $created % self::BATCH_AMOUNT) {
                    $this->em->flush();
                    $this->em->clear();
                }
            }

            $this->em->flush();
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage());
        }

        $this->logger->alert('Result', ['created' => $created, 'updated' => $updated, 'errors' => $errors]);

        return 0;
    }

    /**
     * @param ProductDTO $productDTO
     *
     * @return bool
     */
    protected function isValid(ProductDTO $productDTO): bool
    {
        $violationList = $this->validator->validate($productDTO);
        if ($violationList->count() > 0) {
            foreach ($violationList as $item) {
                $this->logger->error('Validation errors', ['message' => $item->getMessage()]);
            }

            return false;
        }

        return true;
    }

    /**
     * @param Product    $product
     * @param ProductDTO $productDTO
     *
     * @return bool
     */
    protected function isUpdated(Product $product, ProductDTO $productDTO): bool
    {
        if ($product->getDescription() !== $productDTO->description
            || $product->getNormalPrice() !== $productDTO->normalPrice
            || $product->getSpecialPrice() !== $productDTO->specialPrice
        ) {
            $product->setDescription($productDTO->description);
            $product->setNormalPrice($productDTO->normalPrice);
            $product->setSpecialPrice($productDTO->specialPrice);

            return true;
        }

        return false;
    }
}
