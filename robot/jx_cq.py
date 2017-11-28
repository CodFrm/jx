#!/usr/bin/python
# -*- coding: UTF-8 -*-

from socket import *
import urllib2

url='http://d.worldtreestd.com/'
applyAccount =url+'admin/api/apply'
excIntegral=url+'admin/api/exp'
key='aDgea233Emm'
def get(url):
    url=url+"&key="+key
    print url
    data=''
    try:
        response=urllib2.urlopen(url,timeout=20)
        data=response.read()  
        # opear = urllib2.build_opener()
        # req = urllib2.Request(url)
        # res = opear.open(req,timeout=10)
        # data = res.read()
    except urllib2.HTTPError, e:
        if e.getcode()==404:
            data='接口错误!!请联系管理员'
    except:
        print url
    else:
        pass
    return data

if __name__=='__main__':
    HOST='47.104.1.102'
    PORT=1214
    BUFSIZ=1024
    ADDR=(HOST, PORT)
    client=socket(AF_INET, SOCK_STREAM)
    client.settimeout(5)
    client.connect(ADDR)
    while True:
        try:
            data=client.recv(BUFSIZ)
        except:
            data='null'
        if not data:
            break
        client.send('ping')
        deal=data.split('\x00')
        if len(deal)<=1:
            #client.send('错误的参数')
            pass
        elif deal[1]=='1' and len(deal)==3:# 注册账号
            print deal
            getData=get(applyAccount+'?qq='+deal[2])
            print getData
            if len(getData)>50:
                getData='服务器错误'
            client.send(deal[0]+'\x00'+'1'+'\x00'+getData)
            pass
        elif deal[1]=='2' and len(deal)==4:
            print deal
            getData=get(excIntegral+'?user='+deal[2]+'&number='+deal[3])
            print "data:"+getData
            if len(getData)>50:
                getData='服务器错误'
            client.send(deal[0]+'\x00'+'2'+'\x00'+getData)
            pass
        else:
            client.send(deal[0]+'\x00'+'0'+'\x00'+'错误参数')
        pass
    print 'end'