<?php
$models = glob(__DIR__ . '/models/*.php');
$repairedModels = [];
$issuesFixed = [];

foreach ($models as $modelPath) {
    if (basename($modelPath) === 'BaseModel.php') continue;
    $content = file_get_contents($modelPath);
    
    if (strpos($content, 'extends BaseModel') !== false) {
        $originalContent = $content;
        $fixes = [];
        
        // Remove redeclared $db property
        if (preg_match('/(?:private|protected|public)\s+(?:Database\s+)?\$db\s*;/i', $content)) {
            $content = preg_replace('/(?:private|protected|public)\s+(?:Database\s+)?\$db\s*;\s*/i', '', $content);
            $fixes[] = 'Removed redeclared $db property';
        }
        
        // Remove redundant constructor if it ONLY initializes DB or just calls parent
        // Regex to match a simple constructor
        if (preg_match('/public\s+function\s+__construct\s*\(\)\s*\{[\s\n]*(?:parent::__construct\(\);)?[\s\n]*(?:\$this->db\s*=\s*Database::getInstance\(\);)?[\s\n]*\}/i', $content)) {
            $content = preg_replace('/public\s+function\s+__construct\s*\(\)\s*\{[\s\n]*(?:parent::__construct\(\);)?[\s\n]*(?:\$this->db\s*=\s*Database::getInstance\(\);)?[\s\n]*\}/i', '', $content);
            $fixes[] = 'Removed redundant constructor';
        } else {
            // If it has a constructor but it initializes DB, remove the DB line
            if (preg_match('/\$this->db\s*=\s*Database::getInstance\(\);/', $content)) {
                $content = preg_replace('/\$this->db\s*=\s*Database::getInstance\(\);\s*/', '', $content);
                $fixes[] = 'Removed redundant DB initialization from constructor';
            }
        }
        
        // Verify property visibility issues (like private $table)
        if (preg_match('/private\s+string\s+\$table/', $content)) {
            $content = preg_replace('/private\s+string\s+\$table/', 'protected string $table', $content);
            $fixes[] = 'Fixed visibility of $table to protected';
        }
        
        if (preg_match('/private\s+string\s+\$primaryKey/', $content)) {
            $content = preg_replace('/private\s+string\s+\$primaryKey/', 'protected string $primaryKey', $content);
            $fixes[] = 'Fixed visibility of $primaryKey to protected';
        }
        
        if ($content !== $originalContent) {
            file_put_contents($modelPath, $content);
            $repairedModels[] = basename($modelPath);
            $issuesFixed[basename($modelPath)] = $fixes;
        }
    }
}

echo "Repairs complete.\n";
echo "Repaired Models:\n";
foreach ($issuesFixed as $model => $fixes) {
    echo "- $model: " . implode(', ', $fixes) . "\n";
}
