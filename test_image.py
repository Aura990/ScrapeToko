import re
from bs4 import BeautifulSoup

html = open('tokopedia_ayafrozen.html', encoding='utf-16').read()
soup = BeautifulSoup(html, 'html.parser')

products = soup.find_all(lambda tag: tag.name == 'a' and tag.find('img', alt='product-image'))

for p in products[:1]:
    # Let's print all attributes of all img tags
    for img in p.find_all('img'):
        print(img.attrs)
    print("---")
    # Let's see if there is any other tag with a url
    print(p.prettify())
