import re

html = open('tokopedia_ayafrozen.html', encoding='utf-16').read()
# search for 'original":"'
idx = html.find('original":"')
while idx != -1:
    start = max(0, idx - 100)
    end = min(len(html), idx + 200)
    print(html[start:end])
    idx = html.find('original":"', idx + 1)
