.PHONY: win winArm linux

win:
	@echo "Runing on Windows"
ifeq ($(stack),lemp)
	@copy .\stacks\lemp.sh .\install.sh
else
	@copy .\stacks\lamp.sh .\install.sh
endif
	@.\mkcert\mkcert-v1.4.4-windows-amd64.exe -install
	@.\mkcert\mkcert-v1.4.4-windows-amd64.exe --key-file .\cert\my-key.pem  --cert-file .\cert\my-cert.pem *.ex.lc
	@vagrant up

winArm:
	@echo "Runing on Windows"
	# Copy lemp.sh to install.sh
ifeq ($(stack),lemp)
	@copy .\stacks\lemp.sh .\install.sh
else
	@cp .\stacks\lamp.sh .\install.sh
endif
	@.\mkcert\mkcert-v1.4.4-windows-arm64.exe -install
	@.\mkcert\mkcert-v1.4.4-windows-arm64.exe --key-file .\cert\my-key.pem  --cert-file .\cert\my-cert.pem *.ex.lc localhost 127.0.0.1 ::1
	@vagrant up

linux:
	@echo "Runing on Linux"
	# Copy lemp.sh to install.sh
ifeq ($(stack),lemp)
	@cp ./stacks/lemp.sh ./install.sh
else
	@cp ./stacks/lamp.sh ./install.sh
endif
	@sudo cp ./mkcert/mkcert-v1.4.4-linux-amd64 /usr/local/bin/mkcert
	@sudo chmod +x /usr/local/bin/mkcert
	@mkcert -install
	@mkcert --key-file ./cert/my-key.pem  --cert-file ./cert/my-cert.pem *.ex.lc localhost 127.0.0.1 ::1
	@sudo vagrant up
