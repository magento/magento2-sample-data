<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SampleData\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Magento\Framework\App\ObjectManagerFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Console\Cli;
use Magento\Setup\Model\AdminAccount;
use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\State;

/**
 * Command for installing Sample Data
 */
class SampleDataInstallCommand extends Command
{
    /**
     * Name of input option
     */
    const INPUT_KEY_GROUP = 'group';

    /**
     * Object Manager
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Constructor
     *
     * @param ObjectManagerFactory $objectManagerFactory
     */
    public function __construct(ObjectManagerFactory $objectManagerFactory)
    {
        $params[Bootstrap::PARAM_REQUIRE_MAINTENANCE] = null;
        $params[State::PARAM_MODE] = State::MODE_DEVELOPER;
        $this->objectManager = $objectManagerFactory->create($params);
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('sampledata:install')
            ->setDescription('Installs sample data')
            ->setDefinition(
                [
                    new InputArgument(
                        AdminAccount::KEY_USER,
                        InputArgument::REQUIRED,
                        'Store\'s admin username'
                    ),
                    new InputOption(
                        Cli::INPUT_KEY_BOOTSTRAP,
                        null,
                        InputOption::VALUE_REQUIRED,
                        'Add or override parameters of the bootstrap'
                    ),
                ]
            );
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = $input->getArgument(AdminAccount::KEY_USER);
        /** @var \Magento\SampleData\Model\InstallerApp $installerApp*/
        $installerApp = $this->objectManager->create('Magento\SampleData\Model\InstallerApp', ['data' => $user]);
        $installerApp->launch();
        $output->writeln('<info>' . 'Successfully installed sample data.' . '</info>');
    }
}
