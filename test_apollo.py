import re
import json

html = open('tokopedia_ayafrozen.html', encoding='utf-16').read()
match = re.search(r'window\.__cache=({.*?});\s*window', html, re.DOTALL)
if match:
    data = json.loads(match.group(1))
    # print keys
    # find the object for product 16319456540
    print("Found cache")
    for key, val in data.items():
        if '16319456540' in key or (isinstance(val, dict) and '16319456540' in str(val)):
            print(f"\nKey: {key}")
            print(f"Val: {val}")
