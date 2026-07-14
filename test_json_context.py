import re

html = open('tokopedia_ayafrozen.html', encoding='utf-16').read()
idx = html.lower().find("sosis ayam merk jofrans")
if idx != -1:
    # the product name appears multiple times, let's find it inside a script tag
    for m in re.finditer(r"sosis ayam merk jofrans", html.lower()):
        start = max(0, m.start() - 200)
        end = min(len(html), m.start() + 500)
        snippet = html[start:end]
        if "<script" in snippet or '"' in snippet: # likely JSON
            print("\nFound inside string:")
            print(snippet)
