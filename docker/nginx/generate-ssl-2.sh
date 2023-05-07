######################
# Become a Certificate Authority
######################

APP=CertificateAuthory
OUTDIR=cert_data

# Generate private key
openssl genrsa -des3 -out $OUTDIR/$APP.key 2048
# Generate root certificate
openssl req -x509 -new -nodes -key $OUTDIR/$APP.key -sha256 -days 825 -out $OUTDIR/$APP.pem

######################
# Create CA-signed certs
######################

NAME=dapp.local # Use your own domain name
# Generate a private key
openssl genrsa -out $OUTDIR/$NAME.key 2048
# Create a certificate-signing request
openssl req -new -key $OUTDIR/$NAME.key -out $OUTDIR/$NAME.csr
# Create a config file for the extensions
>$OUTDIR/$NAME.ext cat <<-EOF
authorityKeyIdentifier=keyid,issuer
basicConstraints=CA:FALSE
keyUsage = digitalSignature, nonRepudiation, keyEncipherment, dataEncipherment
subjectAltName = @alt_names

[alt_names]
DNS.1 = $NAME # Be sure to include the domain name here because Common Name is not so commonly honoured by itself
DNS.2  = q3.$NAME
DNS.3  = q3-backend.$NAME
DNS.4  = q3-api.$NAME
DNS.5  = q3-eu.$NAME
DNS.6  = q3-eu-backend.$NAME
DNS.7  = q3-eu-api.$NAME
DNS.8  = q3-uk.$NAME
DNS.9  = q3-uk-backend.$NAME
DNS.10 = q3-uk-api.$NAME
EOF

# Create the signed certificate
openssl x509 -req -in $OUTDIR/$NAME.csr -CA $OUTDIR/$APP.pem -CAkey $OUTDIR/$APP.key -CAcreateserial \
-out $OUTDIR/$NAME.crt -days 825 -sha256 -extfile $OUTDIR/$NAME.ext

# generate chrome cert
# openssl pkcs12 -export -inkey ./dapp.local.key -in ./dapp.local.crt -out ./chrome_import_crt.p12