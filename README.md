# 📌 API de Contatos - Desafio Uex.io

### API para gerenciamento de contatos, incluindo cadastro, edição, exclusão, busca e integração com o **Google Maps API** e **ViaCEP** para preenchimento automático de endereços.

---

## 🚀 Tecnologias Utilizadas
- **Laravel 11**
- **PHP 8.4+ (via Docker)**
- **MySQL 8.0+ (via Docker)**
- **Laravel Sail (Ambiente de Desenvolvimento com Docker)**
- **Google Maps API (Busca de endereços e geolocalização)**
- **Sanctum (Autenticação)**
- **ViaCEP (Consulta de Endereços)**
- **PHPUnit (Testes Unitários e de Feature)**
- **Swegger (Documentação de API)**

---

## 📌 Pré-requisitos

Antes de começar, você precisará ter instalado:

- [Docker](https://www.docker.com/get-started) (Recomendado: Docker Desktop)
- [Composer](https://getcomposer.org/download/) (Apenas para instalar o Laravel Sail)
- **Conta no Google Cloud e API Key do Google Maps** ([Criar API Key](https://console.cloud.google.com/))

---

## 📌 Configuração da API do Google Maps

Para usar a integração com o **Google Maps**, é necessário gerar uma **API Key** no Google Cloud.

1️⃣ **Crie uma chave de API** no [Google Cloud Console](https://console.cloud.google.com/)  
2️⃣ **Adicione a chave no `.env`** do projeto:
```env
GOOGLE_MAPS_API_KEY=YOUR_GOOGLE_MAPS_API_KEY_HERE
