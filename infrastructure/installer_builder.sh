#!/bin/bash

#Usage: installer_builder.sh 1.0.0

if (( $# != 1 )); then
    echo -e "Illegal number of parameters"
    exit 1
fi

VERSION=$1
CURRENT_DIR=$(pwd)
TMP_DIR="${CURRENT_DIR}/tmp"

mkdir -p "${TMP_DIR}/bin"
mkdir -p "${TMP_DIR}/config/packages"
mkdir -p "${TMP_DIR}/db"
mkdir -p "${TMP_DIR}/var/cache"

cp "${CURRENT_DIR}/bin/spryker-sdk.sh" "${TMP_DIR}/bin/"
cp "${CURRENT_DIR}/docker-compose."* "${TMP_DIR}/"
cp "${CURRENT_DIR}/config/packages/workflow.yaml" "${TMP_DIR}/config/packages/workflow.yaml"
cp -R "${CURRENT_DIR}/extension" "${TMP_DIR}/"
cp "${CURRENT_DIR}/.env.prod" "${TMP_DIR}/.env.prod"
cp "${CURRENT_DIR}/.gitmodules" "${TMP_DIR}/.gitmodules"
cp -R "${CURRENT_DIR}/infrastructure" "${TMP_DIR}/"

echo "${VERSION}" > "${TMP_DIR}/VERSION"

cd "${TMP_DIR}"
tar cJf spryker-sdk.tar.gz .gitmodules .env.prod bin/ var/ extension/ config/packages/workflow.yaml db/ \
    infrastructure/sdk.Dockerfile infrastructure/sdk.local.Dockerfile infrastructure/sdk.debug.Dockerfile \
    infrastructure/debug/php/69-xdebug.ini VERSION docker-compose.yml docker-compose.debug.yml docker-compose.dev.yml
cd "$CURRENT_DIR"

cat "${TMP_DIR}/infrastructure/installer_header.sh" "${TMP_DIR}/spryker-sdk.tar.gz" > "${CURRENT_DIR}/build/installer.sh"
chmod a+x "${CURRENT_DIR}/build/installer.sh"

# Clean up

rm -rf "${TMP_DIR}"
