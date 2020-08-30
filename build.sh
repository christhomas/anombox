#!/usr/bin/env bash

docker-compose build

docker tag anombox_nginx:latest christhomas/anombox_nginx:latest
docker tag anombox_phpfpm:latest christhomas/anombox_phpfpm:latest

docker push christhomas/anombox_nginx:latest
docker push christhomas/anombox_phpfpm:latest
