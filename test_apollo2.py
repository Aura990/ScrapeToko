import re

html = open('tokopedia_ayafrozen.html', encoding='utf-16').read()
product_id = '16319456540'
idx = html.find(product_id)
if idx != -1:
    start = max(0, idx - 500)
    end = min(len(html), idx + 1000)
    print(html[start:end])
