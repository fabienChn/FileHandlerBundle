# FileHandlerBundle

Helps Handling Files

## Use 

Put this in config/packages/file_handler.yaml:

    file_handler:
        temp_folder: '%kernel.project_dir%/public/uploads/temp'
        upload_folder: '%kernel.project_dir%/public/uploads'

Here is how to use it:

    $file = $uploader->setFile(UploadedFile $file)->upload(); // moving to the temp_folder given in config
---
    $avatarId = $fileHandler->setFile($file)
        ->moveToUploads('avatar') // moving file to an avatar folder in the upload_folder given in config
        ->crop()
        ->convertImageTo('png')
        ->getFileName();
