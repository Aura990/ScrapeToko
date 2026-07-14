import re
import json

html = open('tokopedia_ayafrozen.html', encoding='utf-16').read()
cache_match = re.search(r'window\.__cache=(.*?);\s*window\.', html, re.DOTALL)
if cache_match:
    try:
        cache_data = json.loads(cache_match.group(1))
        # Look for GetShopProduct
        for key in cache_data.keys():
            if 'ShopProducts' in key or 'GetShopProduct' in key:
                print("Found Key:", key)
                products = cache_data[key]['data']['GetShopProduct']['data']
                for p in products[:2]:
                    print("Name:", p['name'])
                    print("Image:", p['primary_image']['original'])
    except Exception as e:
        print("Error parsing:", e)
else:
    print("No cache found")
