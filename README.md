# Contao PDF Metadata
Extends the contao file manager to clean up the metadata of PDF files for privacy reasons.

The following two commands are executed in the prozess:


```console
$ exiftool -all= -Author='' -tagsfromfile @ -title -keywords -subject -description file.pdf -o intermediate.pdf
$ qpdf --linearize intermediate.pdf file.pdf
```

## Requirements
- PHP 8
- [ExifTool](https://github.com/exiftool/exiftool) installed on the system
- [QPDF](https://github.com/qpdf/qpdf) installed on the system

## Configuration

```yaml
# config/config.yaml
contao_pdf_metadata:

    # Overwrites the author field in the cleaned PDF file.
    author:               ''

    # The path to the qpdf binary.
    qpdf_path:            /usr/bin/qpdf

    # The path to the exiftool binary.
    exiftool_path:        /usr/bin/exiftool

    # Clean up the metadata of PDF files immediately after uploading.
    cleanup_on_upload:    false
```

## Console Command
To clean up the metadata of all PDF files in the `files/` directory, you can use the following command:

```console
$ vendor/bin/contao-console contao:pdf-metadata:clean
```