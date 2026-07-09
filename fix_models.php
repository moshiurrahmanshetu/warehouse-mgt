<?php
$modelsDir = __DIR__ . '/models';
$models = [
    'UserModel', 'RoleModel', 'WarehouseModel', 'ZoneModel', 'RackModel', 
    'ShelfModel', 'BinModel', 'SupplierModel', 'CustomerModel', 
    'CategoryModel', 'BrandModel', 'UnitModel', 'TaxRateModel', 
    'CurrencyModel', 'ProductAttributeModel', 'ProductAttributeValueModel', 'ProductTagModel'
];

$signatures = [
    'getAll' => 'public function getAll(array $filters = [], int $limit = 0, int $offset = 0, bool $includeDeleted = false): array',
    'countAll' => 'public function countAll(array $filters = [], bool $includeDeleted = false): int',
    'findById' => 'public function findById(int $id, bool $includeDeleted = false): array|false',
    'delete' => 'public function delete(int $id): void',
    'restore' => 'public function restore(int $id): void',
    'toggleStatus' => 'public function toggleStatus(int $id): void',
    '__construct' => 'public function __construct()',
];

$tableMap = [
    'UserModel' => 'users',
    'RoleModel' => 'roles',
    'WarehouseModel' => 'warehouses',
    'ZoneModel' => 'zones',
    'RackModel' => 'racks',
    'ShelfModel' => 'shelves',
    'BinModel' => 'bins',
    'SupplierModel' => 'suppliers',
    'CustomerModel' => 'customers',
    'CategoryModel' => 'categories',
    'BrandModel' => 'brands',
    'UnitModel' => 'units',
    'TaxRateModel' => 'tax_rates',
    'CurrencyModel' => 'currencies',
    'ProductAttributeModel' => 'product_attributes',
    'ProductAttributeValueModel' => 'product_attribute_values',
    'ProductTagModel' => 'product_tags',
];

foreach ($models as $modelName) {
    $file = $modelsDir . '/' . $modelName . '.php';
    if (!file_exists($file)) continue;

    $content = file_get_contents($file);
    
    // 1. Ensure extends BaseModel
    if (!preg_match('/class\s+' . $modelName . '\s+extends\s+BaseModel/', $content)) {
        $content = preg_replace('/class\s+' . $modelName . '\s*\{/', "class $modelName extends BaseModel\n{", $content);
        $content = preg_replace('/require_once.+;/', "$0\nrequire_once __DIR__ . '/BaseModel.php';", $content);
    }
    
    // 2. Ensure $table property
    if (!preg_match('/protected\s+string\s+\$table/', $content)) {
        $table = $tableMap[$modelName];
        $content = preg_replace('/class\s+' . $modelName . '\s+extends\s+BaseModel\s*\{/', "$0\n    protected string \$table = '$table';", $content);
    }
    
    // 3. Ensure $primaryKey property
    if (!preg_match('/protected\s+string\s+\$primaryKey/', $content)) {
        $content = preg_replace('/protected\s+string\s+\$table\s*=\s*\'[^\']+\';/', "$0\n    protected string \$primaryKey = 'id';", $content);
    }

    // 4. Ensure parent::__construct()
    if (preg_match('/public\s+function\s+__construct\(\)\s*\{/', $content, $matches)) {
        if (!preg_match('/parent::__construct\(\);/', $content)) {
            $content = preg_replace('/(public\s+function\s+__construct\(\)\s*\{)/', "$1\n        parent::__construct();", $content);
        }
    } else {
        // If no construct, add it
        // Wait, if no construct, parent construct is inherited. But the prompt says "Verify every child model correctly calls parent::__construct() if required."
        // We'll just leave it if it doesn't exist, as PHP calls parent automatically if no child constructor exists.
    }

    // Fix $this->db = Database::getInstance(); which is redundant now because parent::__construct() sets it.
    // We can leave it or remove it, but let's just make sure signatures match.

    // 5. Method signatures
    foreach ($signatures as $method => $sig) {
        if ($method === '__construct') continue;
        
        // Match public function method(...) and replace with new signature
        // regex to find the method definition
        $pattern = '/public\s+function\s+' . $method . '\s*\([^\)]*\)\s*(:\s*[\w|\\\\]+)?/';
        
        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, $sig, $content);
        }
    }

    file_put_contents($file, $content);
    echo "Processed $modelName\n";
}
