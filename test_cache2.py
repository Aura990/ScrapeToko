import re
import json

html = open('tokopedia_ayafrozen.html', encoding='utf-16').read()
cache_match = re.search(r'window\.__cache\s*=\s*({.*?});</script>', html, re.DOTALL)
if cache_match:
    try:
        cache_data = json.loads(cache_match.group(1))
        # Look for ShopProducts or similar
        for key in cache_data.keys():
            if 'ShopProduct' in key or 'ShopCore' in key or 'GetShop' in key or 'Product' in key:
                print("Found Key:", key)
                # Dump a bit of this key
                print(str(cache_data[key])[:200])
    except Exception as e:
        print("Error parsing:", e)
else:
    print("No cache found")
