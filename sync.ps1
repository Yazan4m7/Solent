 $source  = 'C:\Users\Yazan\Desktop\cCL\Mine\Generic\html'
     $target  = 'C:\Users\Yazan\Desktop\cCL\Mine\Generic\Korvion'
     $folders = 'app','bootstrap','config','database','public','resources','routes','tests'
     foreach ($folder in $folders) {
         $srcPath = Join-Path $source $folder
         $dstPath = Join-Path $target $folder
         if (Test-Path $srcPath) {
             robocopy $srcPath $dstPath /E /NFL /NDL /NJH /NJS /XF '*.log' /XD 'storage' 'node_modules' 'vendor' '.git'
         }
     }
$files = @(
    'artisan'
    'composer.json'
    'composer.lock'
    'package.json'
    'package-lock.json'
    'phpunit.xml'
    'webpack.mix.js'
    '.env.example'
)
     foreach ($file in $files) {
         $srcFile = Join-Path $source $file
         if (Test-Path $srcFile) {
             Copy-Item $srcFile -Destination $target -Force
         }
     }
