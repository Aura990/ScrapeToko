import re

html = open('tokopedia_ayafrozen.html', encoding='utf-16').read()
matches = re.findall(r'primary_image":{"original":"([^"]+)"', html)
print(f"Found {len(matches)} images")
for m in matches[:5]:
    print(m)
