<?php

namespace Nodes\Api\Scaffolding\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\MountManager;

/**
 * Class Scaffolding.
 */
class Scaffolding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nodes:api:scaffolding 
                            {--namespace= : Namespace of your project}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Nodes API scaffolding';

    /**
     * Laravel filesystem.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Project config.
     *
     * @var array
     */
    protected $projectConfig;

    /**
     * Project folder name.
     *
     * @var string
     */
    protected $projectFolderName;

    /**
     * Path to project folder.
     *
     * @var string
     */
    protected $projectFolderPath;

    /**
     * GenerateScaffolding constructor.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    /**
     * Generate scaffolding.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return void
     */
    public function handle()
    {
        // Set project folder and path
        $this->projectFolderName = $projectFolderName = 'project';
        $this->projectFolderPath = base_path($projectFolderName);

        // Run scaffolding ...
        if ($this->generateStructure()) {
            $this->generateScaffolding();
            $this->call('nodes:api:reset-password');
        }
    }

    /**
     * Ask for the namespace of project.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return string
     */
    public function askForNamespace()
    {
        $namespace = $this->ask('What is the namespace of your project?');
        if (empty($namespace)) {
            return $this->askForNamespace();
        }

        return $namespace;
    }

    /**
     * Generate project structure.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return bool
     */
    protected function generateStructure()
    {
        // Confirm generation of project structure
        if (! $this->confirm('Do you wish to generate Nodes API structure?', true)) {
            return false;
        }

        // Ask for namespace if not provided
        $this->namespace = $namespace = $this->option('namespace');
        if (empty($namespace)) {
            $this->namespace = $namespace = $this->askForNamespace();
        }

        // Create project folder if it doesn't exist
        if (! $this->filesystem->exists($this->projectFolderPath)) {
            $this->filesystem->makeDirectory($this->projectFolderPath, 0755, true);
        }

        // Generate structure folders
        $this->generateControllersFolders();
        $this->generateModelsFolder();
        $this->generateRoutesFolder();

        // Add to Composer's autoload
        add_to_composer_autoload('classmap', 'project');

        return true;
    }

    /**
     * Generate controllers folders.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return void
     */
    protected function generateControllersFolders()
    {
        // Output to console
        $this->comment('Creating controllers folders ...');

        // Create API controller folder and put .gitkeep in there
        $controllersApiPath = sprintf('%s/%s/%s', $this->projectFolderPath, 'Controllers', 'Api');
        if (! $this->filesystem->exists($controllersApiPath)) {
            $this->filesystem->makeDirectory($controllersApiPath, 0755, true);
            $this->generateGitKeep($controllersApiPath);
            $this->line(sprintf('<info>Created folder</info> <comment>[%s]</comment>', sprintf('%s/%s/%s', $this->projectFolderName, 'Controllers', 'Api')));
        } else {
            $this->line(sprintf('<comment>Folder</comment> [%s] <comment>already exists</comment>', sprintf('%s/%s/%s', $this->projectFolderName, 'Controllers', 'Frontend')));
        }

        // Create Frontend controller folder and put .gitkeep in there
        $controllersFrontendPath = sprintf('%s/%s/%s', $this->projectFolderPath, 'Controllers', 'Frontend');
        if (! $this->filesystem->exists($controllersFrontendPath)) {
            $this->filesystem->makeDirectory($controllersFrontendPath, 0755, true);
            $this->generateGitKeep($controllersFrontendPath);
            $this->line(sprintf('<info>Created folder</info> <comment>[%s]</comment>', sprintf('%s/%s/%s', $this->projectFolderName, 'Controllers', 'Frontend')));
        } else {
            $this->line(sprintf('<comment>Folder</comment> [%s] <comment>already exists</comment>', sprintf('%s/%s/%s', $this->projectFolderName, 'Controllers', 'Frontend')));
        }
    }

    /**
     * Generate models folder.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return void
     */
    protected function generateModelsFolder()
    {
        // Output to console
        $this->comment('Creating models folder ...');

        // Create models folders and put .gitkeep in there
        $modelsPath = sprintf('%s/%s', $this->projectFolderPath, 'Models');
        if (! $this->filesystem->exists($modelsPath)) {
            $this->filesystem->makeDirectory($modelsPath, 0755, true);
            $this->generateGitKeep($modelsPath);
            $this->line(sprintf('<info>Created folder</info> <comment>[%s]</comment>', sprintf('%s/%s', $this->projectFolderName, 'Models')));
        } else {
            $this->line(sprintf('<comment>Folder</comment> [%s] <comment>already exists</comment>', sprintf('%s/%s', $this->projectFolderName, 'Models')));
        }
    }

    /**
     * Generate routes folder.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return void
     */
    protected function generateRoutesFolder()
    {
        // Output to console
        $this->comment('Creating routes folders ...');

        // Create API routes folder and put .gitkeep in there
        $routesApiPath = sprintf('%s/%s/%s', $this->projectFolderPath, 'Routes', 'Api');
        if (! $this->filesystem->exists($routesApiPath)) {
            $this->filesystem->makeDirectory($routesApiPath, 0755, true);
            $this->generateGitKeep($routesApiPath);
            $this->line(sprintf('<info>Created folder</info> <comment>[%s]</comment>', sprintf('%s/%s/%s', $this->projectFolderName, 'Routes', 'Api')));
        } else {
            $this->line(sprintf('<comment>Folder</comment> [%s] <comment>already exists</comment>', sprintf('%s/%s/%s', $this->projectFolderName, 'Routes', 'Frontend')));
        }

        // Create Frontend routes folder and put .gitkeep in there
        $routesFrontendPath = sprintf('%s/%s/%s', $this->projectFolderPath, 'Routes', 'Frontend');
        if (! $this->filesystem->exists($routesFrontendPath)) {
            $this->filesystem->makeDirectory($routesFrontendPath, 0755, true);
            $this->generateGitKeep($routesFrontendPath);
            $this->line(sprintf('<info>Created folder</info> <comment>[%s]</comment>', sprintf('%s/%s/%s', $this->projectFolderName, 'Routes', 'Frontend')));
        } else {
            $this->line(sprintf('<comment>Folder</comment> [%s] <comment>already exists</comment>', sprintf('%s/%s/%s', $this->projectFolderName, 'Routes', 'Frontend')));
        }

        // Add route folders to Nodes autoload config
        add_to_autoload_config([
            'project/Routes/Api/',
            'project/Routes/Frontend/',
        ]);
    }

    /**
     * Generate .gitkeep file.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param string $path
     *
     * @return bool
     */
    protected function generateGitKeep($path)
    {
        return (bool) $this->filesystem->put(sprintf('%s/%s', $path, '.gitkeep'), '');
    }

    /**
     * Generate API Scaffolding.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return bool
     */
    protected function generateScaffolding()
    {
        if (! $this->confirm('Do you wish to generate Nodes API scaffolding? <comment>Note: Existing files will be overwritten.</comment>', true)) {
            return false;
        }

        $this->scaffoldUsersController();
        $this->scaffoldUserModel();
        $this->scaffoldUserRepository();
        $this->scaffoldUserValidator();
        $this->scaffoldUserTransformer();
        $this->scaffoldTokenModel();
        $this->scaffoldUserRoutes();
        $this->copyAndRunDatabaseMigrations();

        return true;
    }

    /**
     * Scaffold users controller.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return void
     */
    protected function scaffoldUsersController()
    {
        // Output to console
        $this->comment('Generating Users Controller ...');
        $this->generateStubFile(
            sprintf('%s/../Stubs/UsersController.stub', dirname(__FILE__)),
            sprintf('%s/Controllers/Api/UsersController.php', $this->projectFolderPath)
        );
    }

    /**
     * Scaffold user model.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return void
     */
    protected function scaffoldUserModel()
    {
        // Output to console
        $this->comment('Generating User Model ...');
        $this->generateStubFile(
            sprintf('%s/../Stubs/UserModel.stub', dirname(__FILE__)),
            sprintf('%s/Models/Users/User.php', $this->projectFolderPath)
        );

        // Path to API auth config
        $authConfigPath = config_path('nodes/api/auth.php');

        // Add user model to API config file
        $authConfig = file_get_contents($authConfigPath);
        $authConfig = str_replace("'model' => null", "'model' => ".sprintf('%s\Models\Users\User::class', $this->namespace), $authConfig);
        file_put_contents($authConfigPath, $authConfig);
    }

    /**
     * Scaffold user repository.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return void
     */
    protected function scaffoldUserRepository()
    {
        $this->comment('Generating User Repository ...');
        $this->generateStubFile(
            sprintf('%s/../Stubs/UserRepository.stub', dirname(__FILE__)),
            sprintf('%s/Models/Users/UserRepository.php', $this->projectFolderPath)
        );
    }

    /**
     * Scaffold user validator.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return void
     */
    protected function scaffoldUserValidator()
    {
        $this->comment('Generating User Validator ...');
        $this->generateStubFile(
            sprintf('%s/../Stubs/UserValidator.stub', dirname(__FILE__)),
            sprintf('%s/Models/Users/Validation/UserValidator.php', $this->projectFolderPath)
        );
    }

    /**
     * Scaffold user transformer.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return void
     */
    protected function scaffoldUserTransformer()
    {
        $this->comment('Generating User Transformer ...');
        $this->generateStubFile(
            sprintf('%s/../Stubs/UserTransformer.stub', dirname(__FILE__)),
            sprintf('%s/Models/Users/Transformers/UserTransformer.php', $this->projectFolderPath)
        );
    }

    /**
     * Scaffold token model.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return void
     */
    protected function scaffoldTokenModel()
    {
        $this->comment('Generating Token Model ...');
        $this->generateStubFile(
            sprintf('%s/../Stubs/TokenModel.stub', dirname(__FILE__)),
            sprintf('%s/Models/Users/Tokens/Token.php', $this->projectFolderPath)
        );
    }

    /**
     * Scaffold user routes.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return void
     */
    protected function scaffoldUserRoutes()
    {
        $this->comment('Generating User Routes ...');
        $this->generateStubFile(
            sprintf('%s/../Stubs/UserRoutes.stub', dirname(__FILE__)),
            sprintf('%s/Routes/Api/users.php', $this->projectFolderPath)
        );
    }

    /**
     * Copy and run database migrations.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return void
     */
    protected function copyAndRunDatabaseMigrations()
    {
        $this->comment('Copying database migrations ...');
        $this->copyDirectory(base_path('vendor/nodes/api-scaffolding/database/migrations/auth'), database_path('migrations'));
        $this->copyDirectory(base_path('vendor/nodes/api-scaffolding/database/migrations/email-verifications'), database_path('migrations'));

        $this->deleteLaravelBoilerplate();

        $this->comment('Running database migrations ...');
        $this->call('migrate');
    }

    /**
     * Delete Laravel migration boilerplate.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return void
     */
    protected function deleteLaravelBoilerplate()
    {
        // Files to delete
        $files = [
            'database/migrations/2014_10_12_000000_create_users_table.php',
            'database/migrations/2014_10_12_100000_create_password_resets_table.php',
        ];

        // Delete each file individually
        foreach ($files as $file) {
            // Skip if file doesn't exist
            if (! $this->filesystem->exists(base_path($file))) {
                continue;
            }

            // Delete file
            $this->filesystem->delete(base_path($file));
        }
    }

    /**
     * Publish file to application.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param string $from
     * @param string $to
     *
     * @return void
     */
    protected function copyFile($from, $to)
    {
        // If destination directory doesn't exist,
        // we'll create before copying the config files
        $directoryDestination = dirname($to);
        if (! $this->filesystem->isDirectory($directoryDestination)) {
            $this->filesystem->makeDirectory($directoryDestination, 0755, true);
        }

        // Copy file to application
        $this->filesystem->copy($from, $to);

        // Output status message
        $this->line(
            sprintf('<info>Copied %s</info> <comment>[%s]</comment> <info>To</info> <comment>[%s]</comment>',
            'File', str_replace(base_path(), '', realpath($from)), str_replace(base_path(), '', realpath($to)))
        );
    }

    /**
     * Publish directory to application.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param string $from
     * @param string $to
     *
     * @return void
     */
    protected function copyDirectory($from, $to)
    {
        // Load mount manager
        $manager = new MountManager([
            'from' => new Flysystem(new LocalAdapter($from)),
            'to'   => new Flysystem(new LocalAdapter($to)),
        ]);

        // Copy directory to application
        foreach ($manager->listContents('from://', true) as $file) {
            if ($file['type'] !== 'file') {
                continue;
            }
            $manager->put(sprintf('to://%s', $file['path']), $manager->read(sprintf('from://%s', $file['path'])));
        }

        // Output status message
        $this->line(
            sprintf('<info>Copied %s</info> <comment>[%s]</comment> <info>To</info> <comment>[%s]</comment>',
            'Directory', str_replace(base_path(), '', realpath($from)), str_replace(base_path(), '', realpath($to)))
        );
    }

    /**
     * Generate and save stub file.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param string $stub
     * @param string $destination
     *
     * @return void
     */
    private function generateStubFile($stub, $destination)
    {
        try {
            // Prepare template and replace namespace
            $template = $this->filesystem->get($stub);
            $template = $this->replaceNamespace($template);
        } catch (FileNotFoundException $e) {
            $this->error(sprintf('Could not locate file: %s', $stub));

            return;
        }

        // Retrieve folder for destination
        $destinationFolderPath = substr($destination, 0, strrpos($destination, '/'));

        // Create destination folder if it doesn't exist
        if (! $this->filesystem->exists($destinationFolderPath)) {
            $this->filesystem->makeDirectory($destinationFolderPath, 0755, true);
        }

        // Generate file and save it to project
        $this->filesystem->put($destination, $template);

        $this->line(sprintf('<info>Successfully created</info> <comment>[%s]</comment>', str_replace(base_path(), '', $destination)));
    }

    /**
     * Replace namespace in stub content.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param string $content
     *
     * @return string
     */
    private function replaceNamespace($content)
    {
        return str_replace('DummyNamespace', $this->namespace, $content);
    }

    /**
     * Add "project" to composer file.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return void
     */
    private function addToComposer()
    {
        // File path to composer file
        $composerFilePath = base_path('composer.json');

        // Load and JSON decode composer file
        $composerFile = json_decode(file_get_contents($composerFilePath));
        if (in_array('project', $composerFile->autoload->classmap)) {
            return;
        }

        // Add "project" to composer's classmap
        $composerFile->autoload->classmap[] = 'project';

        // Save changes to composer file
        file_put_contents($composerFilePath, json_encode($composerFile, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
