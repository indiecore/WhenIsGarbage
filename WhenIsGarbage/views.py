from WhenIsGarbage import app
from flask import Flask, jsonify, render_template, request
from datetime import datetime, timedelta
import time

@app.route('/')
def index():

	return render_template('index.html')

@app.route('/blog/')
def blog():
	return 'main blog page'
	
@app.errorhandler(404)
def pagenotfound(e):
	return render_template('404.html'), 404
