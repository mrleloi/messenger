FROM ubuntu:22.04

COPY vagrant-main/install.sh /install.sh
RUN ["chmod", "+x", "/install.sh"]

RUN /install.sh
