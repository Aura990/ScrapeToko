import re

html = open('tokopedia_ayafrozen.html', encoding='utf-16').read()
title_matches = re.findall(r'class="[^"]*prd_link-product-name[^"]*"', html)
print("Title matches:", len(title_matches))

price_matches = re.findall(r'class="[^"]*prd_link-product-price[^"]*"', html)
print("Price matches:", len(price_matches))

link_matches = re.findall(r'<a[^>]*href="[^"]*tokopedia\.com/ayafrozen/[^"]*"', html)
print("Product link matches:", len(link_matches))

# Find the general product wrapper class
# Look for a tags containing product links and their parent divs
from bs4 import BeautifulSoup
soup = BeautifulSoup(html, 'html.parser')
links = soup.find_all('a', href=re.compile(r'tokopedia\.com/ayafrozen/'))
if links:
    print("Found links:", len(links))
    sample_link = links[0]
    print("Link classes:", sample_link.get('class'))
    print("Parent classes:", sample_link.parent.get('class'))
    print("Parent parent classes:", sample_link.parent.parent.get('class'))
