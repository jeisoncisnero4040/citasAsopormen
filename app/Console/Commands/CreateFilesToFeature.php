<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateFilesToFeature extends Command{
    protected $signature = 'app:create-feature {featureName}';
    protected $description = 'Crea archivos para una nueva feature puerto,commandModel,serializer,repository,service,viewmodel,controller,dtos';

    public function handle()
    {
        $featureName = $this->argument('featureName');
        $this->createPort($featureName);
        $this->createCommandModel($featureName);
        $this->createSerializer($featureName);
        $this->createRepository($featureName);
        $this->createService($featureName);
        $this->createViewModel($featureName);
        $this->createController($featureName);
        $this->createDtos($featureName);
        $this->info("Archivos para la feature '$featureName' creados exitosamente.");
    }
    private function createPort(string $featureName): void
    {
        $this->createFileFromStub(
            "app/Interfaces/{$featureName}Port.php",
            "stubs/port.stub",
            ['{{FeatureName}}' => $featureName]
        );
    }
    private function createFileFromStub(string $filePath, string $stubPath, array $replacements): void
    {
        $fullPath = base_path($filePath);

        if (file_exists($fullPath)) {
            $this->error("El archivo '$filePath' ya existe. No se sobrescribirá.");
            return;
        }

        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $stubContent = file_get_contents(base_path($stubPath));

        foreach ($replacements as $placeholder => $replacement) {
            $stubContent = str_replace($placeholder, $replacement, $stubContent);
        }

        file_put_contents($fullPath, $stubContent);

        $this->info("Archivo creado: $filePath");
    }
    private function createCommandModel(string $featureName): void
    {
        $this->createFileFromStub(
            "app/Commands/{$featureName}Command.php",
            "stubs/command-model.stub",
            ['{{FeatureName}}' => $featureName]
        );
    }

    private function createSerializer(string $featureName): void
    {
        $this->createFileFromStub(
            "app/Serializers/{$featureName}Serializer.php",
            "stubs/serializer.stub",
            ['{{FeatureName}}' => $featureName]
        );
    }

    private function createRepository(string $featureName): void
    {
        $this->createFileFromStub(
            "app/Repositories/{$featureName}Repository.php",
            "stubs/repository.stub",
            ['{{FeatureName}}' => $featureName]
        );
    }

    private function createService(string $featureName): void
    {
        $this->createFileFromStub(
            "app/Services/{$featureName}Service.php",
            "stubs/service.stub",
            ['{{FeatureName}}' => $featureName]
        );
    }

    private function createViewModel(string $featureName): void
    {
        $this->createFileFromStub(
            "app/Models/{$featureName}ViewModel.php",
            "stubs/model.stub",
            ['{{FeatureName}}' => $featureName]
        );
    }

    private function createController(string $featureName): void
    {
        $this->createFileFromStub(
            "app/Http/Controllers/{$featureName}Controller.php",
            "stubs/controller.stub",
            ['{{FeatureName}}' => $featureName]
        );
    }
    public function createDtos(string $featureName): void
    {
        $this->createFileFromStub(
            "app/Dtos/Create{$featureName}Dto.php",
            "stubs/dtoCreate.stub",
            ['{{FeatureName}}' => $featureName]
        );
        $this->createFileFromStub(
            "app/Dtos/Get{$featureName}Dto.php",
            "stubs/dtoGet.stub",
            ['{{FeatureName}}' => $featureName]
        );
    }

};