import json
import re

html = open('tokopedia_ayafrozen.html', encoding='utf-16').read()

# Let's search for "sosis ayam merk jofrans" and see surrounding text
idx = html.lower().find("sosis ayam merk jofrans")
if idx != -1:
    print("Found text surrounding product name:")
    start = max(0, idx - 500)
    end = min(len(html), idx + 500)
    print(html[start:end])
    
print("\n---\n")

# Tokopedia usually puts a huge state object in window.__INITIAL_STATE__ or similar
script_matches = re.findall(r'<script>\s*window\.__initialState\s*=\s*(.*?);?\s*</script>', html, re.IGNORECASE)
if script_matches:
    print("Found __initialState")
    try:
        data = json.loads(script_matches[0])
        print("Keys:", data.keys())
    except:
        print("Could not parse JSON")
else:
    print("No __initialState found")

# Let's check for any script containing JSON that has the product name
scripts = re.findall(r'<script[^>]*>(.*?)</script>', html, re.DOTALL | re.IGNORECASE)
for i, script in enumerate(scripts):
    if "sosis ayam merk jofrans" in script.lower():
        print(f"\nFound product name in script {i}:")
        print(script[:1000] + "...")
