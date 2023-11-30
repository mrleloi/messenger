FROM ubuntu:22.04

COPY vagrant-main/install.sh /install.sh

RUN /install.sh
