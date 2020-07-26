<?php

namespace App\Command;

use App\Entity\Product;
use App\Model\Parser\Result;
use App\Model\Parser\Row;
use App\Model\ProductDTO;
use App\Services\Parsers\CsvParser;
use App\Utils\ProductDTOMapper;
use App\Utils\ProductFactory;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

class ImportProductsCommand extends Command
{
    protected static $defaultName = 'app:import-products';

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
    public function __construct(
        CsvParser $parser,
        EntityManagerInterface $em,
        Filesystem $filesystem,
        ValidatorInterface $validator,
        LoggerInterface $logger
    ) {
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
            ->addOption('skip-headers', '', InputOption::VALUE_NONE, 'Skip the first line as line with headers.')
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
        $path = $input->getArgument('path');
        if (!$this->filesystem->exists($path)) {
            $this->logger->error('File not found.');

            return 0;
        }

        $result = Result::create();

        try {
            /** @var Row $row */
            foreach ($this->parser->parse($path, $input->getOption('skip-headers')) as $row) {
                if (!$row->isValid()) {
                    $this->logger->notice(sprintf('Row %d is not valid.', $row->getLine()), $row->getErrors());

                    continue;
                }

                $productDTO = ProductDTOMapper::map($row->getData());
                if (!$this->isValid($productDTO, $row->getLine())) {
                    $result->addError();

                    continue;
                }

                $existed = $this->em->getRepository(Product::class)->findOneBySku($productDTO->sku);
                if ($existed) {
                    $this->update($existed, $productDTO, $result);

                    continue;
                }

                $this->create($productDTO, $result);
            }

            $this->em->flush();
        } catch (Throwable $exception) {
            $this->logger->error($exception->getMessage());
        }

        $this->logger->alert('Result', $result->toArray());

        return 0;
    }

    /**
     * @param ProductDTO $productDTO
     * @param int        $line
     *
     * @return bool
     */
    protected function isValid(ProductDTO $productDTO, int $line): bool
    {
        $violationList = $this->validator->validate($productDTO);
        if ($violationList->count() > 0) {
            $errors = [];
            foreach ($violationList as $item) {
                $errors[$item->getPropertyPath()][] = $item->getMessage();
            }

            $this->logger->notice(sprintf('Product for Row %d is not valid.', $line), $errors);

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

    /**
     * @param ProductDTO $productDTO
     * @param Result     $result
     */
    protected function create(ProductDTO $productDTO, Result $result): void
    {
        $product = ProductFactory::fromProductDTO($productDTO);

        $this->em->persist($product);
        $this->em->flush();
        $this->em->clear();

        $result->addCreated();
    }

    /**
     * @param Product    $existed
     * @param ProductDTO $productDTO
     * @param Result     $result
     */
    protected function update(Product $existed, ProductDTO $productDTO, Result $result): void
    {
        if (!$this->isUpdated($existed, $productDTO)) {
            $result->addSkipped();

            return;
        }

        $this->em->flush();
        $this->em->clear();

        $result->addUpdated();
    }
}
