import re
from bs4 import BeautifulSoup

html = open('tokopedia_ayafrozen.html', encoding='utf-16').read()
soup = BeautifulSoup(html, 'html.parser')

products = soup.find_all(lambda tag: tag.name == 'a' and tag.find('img', alt='product-image'))
print("Found products:", len(products))

for p in products[:1]:
    print("Link:", p.get('href'))
    img = p.find('img', alt='product-image')
    print("Image:", img.get('src'))
    
    # Title could be the first span text
    span = p.find('span')
    if span and span.text.strip():
        print("Title span:", span.text.strip())
    else:
        # maybe another span
        spans = p.find_all('span')
        for s in spans:
            if s.text.strip():
                print("Title span:", s.text.strip())
                break
                
    # Price
    price_div = p.find(lambda tag: tag.name == 'div' and 'Rp' in tag.text)
    if price_div:
        print("Price:", price_div.text.strip())
