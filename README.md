# Whatsapp Chat Data
> Cria estatísticas de conversas do Whatsapp

## Necessário
* Servidor web;
* Arquivo `wppdata.php`;
* Conversa exportada do Whatsapp.

## Servidor web
Existem serviços que oferecem servidores web online, mas sua conversa será exposta num arquivo `.txt` ao público que acessar seu site.
Portanto é uma alternativa não recomendada.

Eu utilizei o [XAMPP](https://www.apachefriends.org/pt_br/index.html) para rodar localmente.

## Exportar conversa
Para que os gráficos sejam gerados, é necessário um arquivo `.txt` da conversa a ser analisada.
Para gerá-lo basta abrir a conversa que quiser exportar, clicar nos três pontos no canto direito superior e seguir: Mais > Exportar conversa.

* OBS: Não precisa incluir mídia, pois o código só usa a conversa em texto como dado. Apesar disso, ele contabiliza mídia para gerar os resultados.

Dependendo do tamanho da conversa, essa exportação pode demorar. Então uma janela de compartilhamento irá abrir para você escolher onde irá guarda-lo. No meu caso, eu escolho o [Google Drive](https://www.google.com.br/drive/apps.html), pois fica bem mais fácil de acessar o arquivo e transferi-lo do celular para o computador.

## Configuração
1. Para que o código rode sobre sua conversa, baixe o arquivo `wppdata.php` e coloque no seu servidor web.
2. Em seguida pegue o arquivo da sua conversa exportada em `.txt` e renomeie para `conversa.txt`.
O arquivo que deve ter o nome mudado tem esse formato inicialmente: Conversa do WhatsApp com [NOME DA CONVERSA].txt
3. Coloque o arquivo `conversa.txt` na mesma pasta que o `wppdata.php`
4. Acesse o `wppdata.php` pelo servidor web e veja os histogramas do seu site

Mais informações estão disponíveis na página do `wppdata.php` ao ser aberta no servidor web.
