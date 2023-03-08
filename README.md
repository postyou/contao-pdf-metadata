# Contao PDF Metadata
Extends the contao file manager to clean up the metadata of PDF files for privacy reasons.

[![Packagist Version](https://img.shields.io/packagist/v/postyou/contao-pdf-metadata)](https://packagist.org/packages/postyou/contao-pdf-metadata)

The following two commands are executed in the prozess:


```console
$ exiftool -all= -Author='' -tagsfromfile @ -title -keywords -subject -description file.pdf -o intermediate.pdf
$ qpdf --linearize intermediate.pdf file.pdf
```

## Requirements
- [ExifTool](https://github.com/exiftool/exiftool) installed on the system
- [QPDF](https://github.com/qpdf/qpdf) installed on the system

## Configuration

```yaml
# config/config.yaml
contao_pdf_metadata:
    exiftool:

        # Path to the exiftool binary.
        path:                 /usr/bin/exiftool

        # Environment variables when running exiftool.
        env:

            # Prototype
            name:                 ~
    qpdf:

        # Path to the qpdf binary.
        path:                 /usr/bin/qpdf

        # Environment variables when running qpdf.
        env:

            # Prototype
            name:                 ~

    # Clean up the metadata of PDF files immediately after uploading.
    cleanup_on_upload:    false

    # Overwrites metadata fields in the cleaned PDF file.
    metadata:
        author:               ''
```

## Console Command
To clean up the metadata of PDF files in the `files/` directory, you can use the following command:

```console
$ vendor/bin/contao-console pdf-metadata:clean [<path>]
```