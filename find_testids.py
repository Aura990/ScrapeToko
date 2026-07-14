import re
import json

html = open('tokopedia_ayafrozen.html', encoding='utf-16').read()
# Find testids that might be product related
testids = set(re.findall(r'data-testid=[\"\']([^\"\']+)[\"\']', html))
product_testids = [t for t in testids if 'product' in t.lower() or 'prd' in t.lower() or 'card' in t.lower()]

print(json.dumps(product_testids, indent=2))
