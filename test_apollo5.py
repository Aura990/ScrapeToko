import re

html = open('tokopedia_ayafrozen.html', encoding='utf-16').read()
names = re.findall(r'"name":"([^"]+)"', html)
images = re.findall(r'primary_image":{"original":"([^"]+)"', html)

# print names that look like products
product_names = []
for n in names:
    if len(n) > 10 and '{' not in n and '\\' not in n:
        product_names.append(n)

print(f"Found {len(product_names)} product names")
for n in product_names[:10]:
    print(n)

print("\n---")
print(f"Found {len(images)} images")
