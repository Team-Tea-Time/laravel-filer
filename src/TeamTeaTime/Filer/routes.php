<?php

get('file/{$id}/download', ['as' => 'filer.file.download', 'uses' => 'FileController@download']);
