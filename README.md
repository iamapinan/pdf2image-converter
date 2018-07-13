# Convert PDF to image converter.
 To convert PDF file to jpg or png using API supported by GET, POST method. This package is included Dockerfile and docker image.

## Getstart using docker
- ``` docker run -d -p 80:80 iamapinan/pdf2image-converter```
- access url by ```localhost/?url=<pdf_file_url>&format=png```

## Getstart usign composer
- Install library using composer
```BASH
composer require iotech/pdf2image-converter
```
- Implement to your project by
```PHP
$pdfc = PDF2Image\Convert::Run($file, $type, $output_to);
```

## Parameter supported
```
url    = Url of pdf
format = Convert file type support jpg, png
```

## License
GNU 2.0

## Author
- Apinan Woratrakun <apinan@iotech.co.th>

## Powered by
IOTech Enterprise Co.,Ltd.