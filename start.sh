#!/bin/bash

echo "#################################################"
echo "  Importando estrutura do Banco de Dados da API  "
echo "#################################################"

sudo mysqldump -u root -pMYPASSWORD123 < short-url.sql

echo -e "\n"

echo "########################################"
echo "  Banco de Dados Importado com Sucesso  "
echo "########################################"
