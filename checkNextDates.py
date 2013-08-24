import MySQLdb
import scrapeAddress
import json

def main():
	db = MySQLdb.connect(host="192.168.0.121", user="hackathon", 
						passwd="hackathon", db="hackathon-garbage")
	c = db.cursor()
	d = db.cursor()
	
	binquery = """SELECT wastecompost, county, street_no, street_nm, comm_nm FROM `hackathon-garbage`.civics WHERE wastecompost != "" GROUP BY wastecompost"""
	bluebagquery = """SELECT bluebag, county, street_no, street_nm, comm_nm FROM `hackathon-garbage`.civics WHERE bluebag != "" GROUP BY bluebag"""
	
	updatequery =  """REPLACE INTO `hackathon-garbage`.civics_zones  (name, next_bluebag, next_blackbin, next_greenbin) VALUES (%s,%s,%s,%s)"""
	
	c.execute(binquery)
	writer = db.cursor()
	
	addresses = c.fetchall()
	#print updatequery % (testAddr[0], info['recycle'],info['green'],info['black'],testAddr[0])
	binList = []
	for address in addresses:
		info = scrapeAddress.scrapeTuple(address[1:])
		binList.append(((address[0], info['recycle'],info['green'],info['black'])))
		if(info['black'] == ''):
			print info
	writer.executemany(updatequery, binList)
	
	d.execute(bluebagquery)
	addresses = d.fetchall()
	blueList = []
	for address in addresses:
		info = scrapeAddress.scrapeTuple(address[1:])
		blueList.append(((address[0], info['recycle'],info['green'],info['black'])))
	writer.executemany(updatequery, blueList)
	
	db.commit()
	return 0
	
main()
