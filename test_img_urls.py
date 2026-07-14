import re

html = open('tokopedia_ayafrozen.html', encoding='utf-16').read()
urls = set(re.findall(r'https://images\.tokopedia\.net/[^\s"\'\\]+\.(?:jpg|png|webp)', html))
print(f"Found {len(urls)} image urls:")
for u in list(urls)[:10]:
    print(u)
