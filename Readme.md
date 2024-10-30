# TYPO3 Extension ``pb_fileinfo`` 

## Introduction

This extension adds infos to linked files in your website (filetype and -size). For example: document.pdf (Pdf-file, 2.1 MB)

## Administration

### Installation

Install this extension via composer
    
    composer req peterbenke/pb-fileinfo

    => Then include the static Typoscript in your template

### Configuration

You can change the following values by the Constant-Editor:

* Wrap
* Write the fileinfo outside or within the a-tag

By default, the following file-extensions are considered:

* pdf
* doc
* xls
* zip
* mp3
* mp4

You can expand this list by typoscript. Here you see the whole typoscript, which comes from the extension:

    tx_pb_fileinfo {

        enable = 1
        wrap = {$tx_pb_fileinfo.wrap}
        # inner: within the a-tag
        # outer: outside the a-tag
        mode = {$tx_pb_fileinfo.mode}
        fileInfos{
            # on the left-side: file-extension
            # %s is the placeholder for the filesize
            pdf = (Pdf-file, %s)
            doc = (Word-file, %s)
            xls = (Excel-file, %s)
            zip = (Zip-file, %s)
            mp3 = (MP3-file, %s)
            mp4 = (MP4-file, %s)
        }

    }
    [globalVar = GP:L=0]
        tx_pb_fileinfo {
    
            fileInfos{
                pdf = (Pdf-Datei, %s)
                doc = (Word-Datei, %s)
                xls = (Excel-Datei, %s)
                zip = (Zip-Datei, %s)
                mp3 = (MP3-Datei, %s)
                mp4 = (MP4-Datei, %s)
            }
    
        }
    [global]
    [globalVar = GP:L=1]
        tx_pb_fileinfo {
    
            fileInfos{
                pdf = (Pdf-file, %s)
                doc = (Word-file, %s)
                xls = (Excel-file, %s)
                zip = (Zip-file, %s)
                mp3 = (MP3-file, %s)
                mp4 = (MP4-file, %s)
            }
    
        }
    [global]


%s is the placeholder for the calculated filesize.

Note:
This is not the most beautiful way to distinguish between the languages, but there is more flexibility to add new file-extensions.
You just have to adjust your ids from your sys-language-records.