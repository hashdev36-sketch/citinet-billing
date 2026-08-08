from pathlib import Path
src = Path(r'C:\Users\hashb\laravel\laravel\config')
dst = Path(r'C:\Users\hashb\laravel\citinet-billing - 2\config')
files = [
    'app.php',
    'cache.php',
    'database.php',
    'filesystems.php',
    'logging.php',
    'mail.php',
    'queue.php',
    'session.php',
    'view.php',
]
for f in files:
    src_file = src / f
    dst_file = dst / f
    if src_file.exists():
        dst_file.write_text(src_file.read_text(encoding='utf-8'), encoding='utf-8')
        print(f'copied {f}')
    else:
        print(f'missing source {f}')
print('done')
