import requests
import json
import argparse
import sys
from lxml import etree, html
from urllib import quote_plus, urlencode

def scrapeAddress(county, street_num, street_name, community_name):
	#url = 'http://www.gov.pe.ca/civicaddress/locator/details.php3?county=PRN&street_no=2&street_nm=WATER%20ST&comm_nm=SUMMERSIDE'
	searchString = urlencode({'county': county, 'street_no':street_num, 'street_nm' : street_name, 'comm_nm' : community_name})
	url = 'http://www.gov.pe.ca/civicaddress/locator/details.php3?'+searchString

	r = requests.get(url)
	#r.text
	#print r.content
	root = html.fromstring(r.text)
	
	upcomingGreenXPath = '//*[@id="content_2c"]/font[6]/table/tr[3]/td[2]/font/text()[2]'
	#//*[@id="content_2c"]/font[6]/table/tbody/tr[3]/td[2]/font/text()[2]
	#//*[@id="content_2c"]/font[6]/table/tbody/tr[3]/td[2]/font/text()[2]
	
	upcomingBlackXPath = '//*[@id="content_2c"]/font[6]/table/tr[3]/td[2]/font/text()[3]'
	#//*[@id="content_2c"]/font[6]/table/tbody/tr[3]/td[2]/font/text()[3]
	#//*[@id="content_2c"]/font[6]/table/tbody/tr[3]/td[2]/font/text()[3]
	
	upcomingRecycleThisMonthXPath = '//*[@id="content_2c"]/font[6]/table/tr[2]/td[2]/font/text()[1]'
	#upcomingRecycleNextMonthXPath = '//*[@id="content_2c"]/font[6]/table/tr[2]/td[2]/font/text()[2]'
	upcomingGreen = root.xpath(upcomingGreenXPath)
	
	if(upcomingGreen[0].strip(' ') == '\r\n'):
		upcomingGreenXPath = '//*[@id="content_2c"]/font[6]/table/tr[3]/td[2]/font/text()[4]'
		upcomingGreen = root.xpath(upcomingGreenXPath)
		
	upcomingBlack = root.xpath(upcomingBlackXPath)
	upcomingRecycleThisMonth = root.xpath(upcomingRecycleThisMonthXPath)
	
	if('Green' in upcomingBlack[0]):
		upcomingBlackXPath = '//*[@id="content_2c"]/font[6]/table/tr[3]/td[2]/font/text()[4]'
		upcomingBlack = root.xpath(upcomingBlackXPath)
	if('Black' in upcomingGreen[0]):
		upcomingGreenXPath = '//*[@id="content_2c"]/font[6]/table/tr[3]/td[2]/font/text()[3]'
		upcomingGreen = root.xpath(upcomingGreenXPath)
	
	if(upcomingBlack[0] == ' (Black Cart)'):
		upcomingBlackXPath = '//*[@id="content_2c"]/font[6]/table/tr[3]/td[2]/font/b/font/text()'
		upcomingBlack = root.xpath(upcomingBlackXPath)
		#upcomingBlack[0].strip('*')
		
	if(upcomingGreen[0] == ' (Green Cart)'):
		upcomingGreenXPath ='//*[@id="content_2c"]/font[6]/table/tr[3]/td[2]/font/b/font/text()'
		upcomingGreen = root.xpath(upcomingGreenXPath)
		#print upcomingGreen[0]
		
	return {'green': upcomingGreen[0].strip(' (Green Cart)'), 'black' : upcomingBlack[0].strip(' (Black Cart)'), 'recycle' : upcomingRecycleThisMonth[0].strip(' \r\nThis Month:')}

def scrapeTuple(addr_tuple):
	return scrapeAddress(addr_tuple[0],str(addr_tuple[1]),addr_tuple[2],addr_tuple[3])
