"""Build the installable theme without modifying the validated static site."""
from pathlib import Path
import shutil, zipfile
root=Path(__file__).resolve().parents[1]
theme=root/'wordpress/hakim-taleb'
for name in ['index.html','kinesiologie.html','mentions-legales.html','confidentialite.html','style.css','favicon.png','gsap.min.js','ScrollTrigger.min.js','blog','img','fonts','docs']:
 source=root/name; dest=theme/'site'/name
 if source.is_dir():shutil.copytree(source,dest,dirs_exist_ok=True)
 else:shutil.copy2(source,dest)
(root/'dist').mkdir(exist_ok=True)
with zipfile.ZipFile(root/'dist/hakim-taleb-wordpress.zip','w',zipfile.ZIP_DEFLATED) as z:
 for p in sorted(theme.rglob('*')):
  if p.is_file():z.write(p,p.relative_to(theme.parent))
print('dist/hakim-taleb-wordpress.zip')
