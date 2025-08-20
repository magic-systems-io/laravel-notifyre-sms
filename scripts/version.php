<?php

class VersionUpdater
{
    private string $composerFile;

    private array $composerJson;

    private string $currentVersion;

    public function __construct()
    {
        $this->composerFile = dirname(__DIR__) . '/composer.json';
        $this->composerJson = json_decode(file_get_contents($this->composerFile), true);
        $this->currentVersion = $this->composerJson['version'] ?? '1.0.0';
    }

    public function update(string $type): void
    {
        $newVersion = $this->incrementVersion($type);

        $this->updateComposerJson($newVersion);

        $this->createGitTag($newVersion);

        echo "Version updated to $newVersion\n";
        echo "Don't forget to push your changes:\n";
        echo "    git push && git push --tags\n";
    }

    private function incrementVersion(string $type): string
    {
        $versionParts = explode('.', $this->currentVersion);
        [$major, $minor, $patch] = array_map('intval', $versionParts);

        switch ($type) {
            case 'major':
                $major++;
                $minor = 0;
                $patch = 0;
                break;
            case 'minor':
                $minor++;
                $patch = 0;
                break;
            case 'patch':
                $patch++;
                break;
            default:
                throw new InvalidArgumentException('Invalid version type. Use major, minor, or patch.');
        }

        return "$major.$minor.$patch";
    }

    private function updateComposerJson(string $version): void
    {
        $this->composerJson['version'] = $version;
        file_put_contents(
            $this->composerFile,
            json_encode($this->composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n"
        );
    }

    private function createGitTag(string $version): void
    {
        if (!is_dir(dirname(__DIR__) . '/.git')) {
            echo "Warning: Not a git repository\n";

            return;
        }

        $version = "v.$version";

        exec('git add composer.json');
        exec(sprintf('git commit -m "Version bump to %s"', $version));
        exec(sprintf('git tag -a %s -m "Version %s"', escapeshellarg($version), escapeshellarg($version)));
    }
}

$type = $argv[count($argv) - 1] ?? null;

if (!in_array($type, ['major', 'minor', 'patch'])) {
    exit("Usage: composer version-[major|minor|patch]\n");
}

new VersionUpdater()->update($type);
