FROM ubuntu:22.04
USER root
COPY vagrant-main/install.sh /install.sh

CMD ["/install.sh"]