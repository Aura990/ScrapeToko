import re
from bs4 import BeautifulSoup

html = open('tokopedia_ayafrozen.html', encoding='utf-16').read()
soup = BeautifulSoup(html, 'html.parser')

links = soup.find_all('a', href=re.compile(r'tokopedia\.com/ayafrozen/'))
if links:
    sample_link = links[0]
    print(sample_link.prettify())
