import requests
import json
import argparse
import sys
import scrapeAddress

from lxml import etree, html
from urllib import quote_plus, urlencode


#sets up the argument parser with correct flags, functionalized to keep shit out of the main
def setupParser():
    
    parser = argparse.ArgumentParser(description = "Scrapes the Government of PEI site for Waste Watch and returns the next pickup dates for the given address")
    
    parser.add_argument('-c','--county',help="The County: PRN, QUN or KNS")
    parser.add_argument('-n','--number',help="The street number of the address")
    parser.add_argument('-s','--street',help="The street name of the address")
    parser.add_argument('-t','--town',help="The Town of the address")
    return parser

def main():
	#county=PRN&street_no=2&street_nm=WATER%20ST&comm_nm=SUMMERSIDE'
	#print scrapeAddress('PRN', '2', 'WATER ST', 'SUMMERSIDE')
	
	parser = setupParser()
	namespace = parser.parse_args(sys.argv[1:])
	argsDict = vars(namespace)
    	
	print json.dumps(scrapeAddress.scrapeAddress(argsDict['county'],argsDict['number'],argsDict['street'],argsDict['town']))
	
#county, street_num, street_name, community_name
#~ def scrapeAddress(county, street_num, street_name, community_name):
	#~ url = 'http://www.gov.pe.ca/civicaddress/locator/details.php3?county=PRN&street_no=2&street_nm=WATER%20ST&comm_nm=SUMMERSIDE'
	#~ searchString = urlencode({'county': county, 'street_no':street_num, 'street_nm' : street_name, 'comm_nm' : community_name})
	#~ url = 'http://www.gov.pe.ca/civicaddress/locator/details.php3?'+searchString
#~ 
	#~ r = requests.get(url)
	#~ #r.text
	#~ #print r.content
	#~ root = html.fromstring(r.text)
	#~ 
	#~ upcomingGreenXPath = '//*[@id="content_2c"]/font[6]/table/tr[3]/td[2]/font/text()[3]'
	#~ 
	#~ upcomingBlackXPath = '//*[@id="content_2c"]/font[6]/table/tr[3]/td[2]/font/text()[4]'
	#~ upcomingRecycleThisMonthXPath = '//*[@id="content_2c"]/font[6]/table/tr[2]/td[2]/font/text()[1]'
	#~ #upcomingRecycleNextMonthXPath = '//*[@id="content_2c"]/font[6]/table/tr[2]/td[2]/font/text()[2]'
	#~ 
	#~ upcomingGreen = root.xpath(upcomingGreenXPath)
	#~ upcomingBlack = root.xpath(upcomingBlackXPath)
	#~ upcomingRecycleThisMonth = root.xpath(upcomingRecycleThisMonthXPath)
	#~ 
	#~ return {'green': upcomingGreen[0].strip(' (Green Cart)'), 'black' : upcomingBlack[0].strip(' (Black Cart)'), 'recycle' : upcomingRecycleThisMonth[0].strip(' \r\nThis Month:')}

main()
