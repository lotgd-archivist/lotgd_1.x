<?php
// translator ready
// addnews ready
// mail ready
// PRIVATE_CODE
/*
//uncomment this block to generate a new data table.  We're employing very weak xor obfuscation of the data.
for ($x=0; $x<1024; $x++){
	$d .= "\x".dechex(mt_rand(0,255));
}
echo $d;
//*/
	$datatable = "\x45\x48\x61\x86\xf4\x91\x58\xa0\x8d\x3b\x83\x7\xc6\x3a\xa2\x2a\x1a\x4b\x52\xba\x2d\x5a\x32\xb1\xcf\xdb\x88\x49\x8f\x21\xa3\x80\xd6\xfd\x35\x61\xd9\x2a\xe\x38\x79\xb5\x6d\xfc\xd2\xe1\xae\xab\x8e\x5b\x3c\x1d\x50\x27\xca\x75\x8b\x9c\xe4\x9d\x75\xff\xce\x4e\xbd\xa8\xfe\x36\xde\x4c\x3e\xc1\x59\xaa\x9c\xe0\xa2\xf8\x28\x92\xdb\x8e\x43\x8c\xe8\x6b\x7d\x71\xf9\x94\x60\x26\x9f\xe6\x8c\xbc\x57\x6e\xc8\xc2\x14\x1c\x2a\xc9\xee\x40\xbe\xde\xb8\x3f\x2d\x6b\x8c\x93\x52\x7a\x9e\xd2\x6f\x30\x5\x9b\x84\xa5\x2e\xa2\x32\x76\xe0\xb2\xbd\xa5\x12\x25\xf7\x4d\x1b\x59\x6a\xcc\xc5\x7d\x1c\x75\x6d\xde\xc1\x61\x16\x14\x7d\xf2\xee\x28\x30\x6b\x4a\xfc\x9b\x19\x49\x87\x80\x74\x3\x7e\xee\xc4\x66\xd2\x8c\x82\x6d\x84\x7d\x1\x5d\x54\xa6\x41\x2e\x8a\x47\x7e\x6e\x3\x6c\x72\x63\x22\x1b\xea\x52\x62\x69\x41\x1e\x97\x7d\x9d\x3d\x6e\x35\x12\xde\xf7\x4c\x13\xb5\xf7\xbe\x34\xa7\x9c\xb7\xc5\xa6\xab\x34\x8e\xec\xff\x23\xaf\xfa\x45\xc5\xe2\x4\xae\x7e\xf2\x3c\xa8\xd3\xc3\x6d\x65\xde\x1d\x69\x29\x8f\x6b\xc\x61\x7a\xdc\x54\x76\x7e\xbd\x79\x2c\xca\x1\xd3\x5e\xfd\x29\xc8\x67\xd3\xfa\x2e\x8f\x7d\x5a\xf5\x13\xff\x83\x28\xd4\x6b\xb\xc0\x4f\x34\x8c\xac\xb5\xc5\xea\x32\xd2\xe6\x1a\x4a\xe5\x84\x8f\xbd\xdb\xf9\x4c\x93\xe2\x71\x2a\xe4\xd5\x34\x65\x71\xe8\x76\xab\xbd\x4\x99\x59\xa8\xdb\xfa\x81\xea\xfb\x89\xe1\x3a\x71\x55\xa2\x87\x2f\xc9\xf7\xdf\xe7\x2e\xaf\x87\x88\x11\xe3\xa2\xc7\x23\xaf\x98\x9b\x1c\x22\x46\x9\xff\xbf\x30\x5c\xca\x45\xa0\xd8\xec\x46\xd6\x7e\xd4\xdf\x73\x6\x38\x49\x18\x9e\xed\x93\x3c\x14\x8e\xd\xd0\x59\xc9\x61\x98\xd5\xa3\xb8\xa1\x3e\x5a\x53\xb7\xc8\x55\x54\xb2\x26\xdf\x5d\x53\x8b\x92\x66\x52\xa0\x7\x39\xc5\xb7\xae\xda\x19\x42\xeb\xa5\x69\x97\x18\x82\xee\x56\x2d\x9b\x41\xb1\xae\xea\x1d\x8d\xa8\xa7\x6e\x47\x88\xa4\x3b\x86\xc2\x96\x7a\x23\xb3\x26\x40\x8c\x64\xcd\xf6\x3b\xc6\xab\x4a\xc3\xfb\x7a\xc9\x20\x49\xa4\x1c\x6\x1f\x96\x15\x59\xd3\x7\x3d\x82\xf8\xa4\x15\x43\x29\x6f\x9\xdd\xa6\x10\x38\x53\x79\x69\x51\xe6\x70\xa0\x9f\x88\x32\x15\xdc\xb2\xa1\xe7\xd4\x66\x92\x2\x2b\x73\xf\xdf\xaf\x1a\x26\xf7\x3b\x4\x3c\x11\x5\x4\xa\x8e\x44\x9d\x7\x7b\xae\x87\x9f\x5b\x59\xf8\x98\x7d\x2b\x83\x4e\x2c\xc3\x27\xa8\xfb\xbd\xfa\x12\x4f\x94\xd6\xf0\x1c\xa3\x7b\xa9\xe6\x72\x1f\x32\x94\x25\xc4\xcc\xed\xb0\x38\xa0\x3d\xc3\x60\x5\x23\x19\x42\xc2\xf2\x66\xfa\xf3\x48\x4\xcd\xab\x74\x5c\xf7\x4d\xd7\xc0\x47\x5f\xbc\x1b\x44\x94\xd9\xf9\x1e\xcf\x43\x67\xc2\x3d\xe8\x7\x9d\x39\xd1\xfd\x87\x16\x17\xc6\xa2\x91\xd1\x97\xa2\xe7\x2\xd0\xe7\x6b\x3f\x72\x5e\x8f\xda\x4b\xd6\xd8\xb4\xb9\x5c\xe5\xc4\x8\x48\xc4\xc9\x8d\x16\x57\x35\xa1\x8e\xd4\x67\x1e\x83\xb\x2b\xaf\x4f\x70\xb1\xd2\x19\xe6\x55\xf2\xb8\x6b\xdf\xd3\xad\x87\x4d\x8c\x80\x2d\x4\xe\xd9\x55\xd3\x74\xd\x7\x98\x20\x7f\x1c\x63\xa8\xc6\xe9\x2e\x9a\xa0\x12\x56\x5a\x6\x2\x86\x9f\x5b\x2c\xd4\x67\x12\x68\x4\xc\x93\xa\xa7\x46\x5\x2b\x67\xd2\x69\x78\x6d\x46\xab\xec\x1\xdf\x37\x27\x1a\x62\xce\xc5\xbd\x89\x72\x6f\xaa\xee\x66\x5b\xe2\x3d\xe0\x31\x1a\x9d\x6b\x49\x5d\xff\x1d\x1e\x83\x3b\x9e\x35\x1d\x92\x7e\x73\xa5\xee\xb1\xdd\xeb\xf4\x32\x84\x89\xf7\x8e\x2f\x56\x76\x41\x78\x3f\x72\x78\x53\x9d\xf8\xb4\x37\xc7\x4c\x4c\xc9\x5f\xac\x70\xfc\x13\xee\x32\xb7\xdc\x14\xf3\xb1\xe2\xd0\xda\xfe\x22\x82\x42\x7b\x4f\xb6\x4a\xe3\x8c\x69\xae\x4f\x9f\x3b\x96\x42\xd7\x73\xae\x5a\xd7\xb3\xd9\xf7\x42\x93\x5\x76\x6e\x3e\x6c\xa8\x92\x84\x82\x2d\x87\x37\x5b\xd5\xe3\xad\x1\xb9\x7\xfd\x35\xed\xff\x7a\x39\x18\x94\x3d\x9b\x44\x8e\x9c\xf2\x8d\xe4\x25\x36\xe4\x47\x1c\x34\x69\x98\x2a\x4a\x4d\xb5\x8b\xb7\x63\x26\x2a\x89\x26\x41\x3e\x2\x60\x8c\x5e\xb3\x71\x5e\xd3\x79\xe2\x5d\xeb\xff\x7c\xa4\x4d\x1d\xe\x47\xa7\x55\x71\x9a\xcd\x96\x2b\xc\xc0\x33\x87\x36\x72\x68\x42\x39\x44\xf5\x23\x49\xf9\x44\xb2\x96\xd8\xe0\x9\x25\x45\xde\xcd\xfc\x9f\xc9\xd\xe7\x93\x4f\x35\x52\x92\x3a\xc7\x54\x82\x78\x4b\xbd\x93\xb1\x31\x8d\x32\x3e\x8f\x3f\xb7\x44\xda\x2b\xb6\x3f\x7a\x5d\xbc\xdb\x55\x50\xaa\xc0\x37\xc8\x2f\xf9\xda\xc0\x64\x38\x57\x26\x8b\x1c\xc0\x46\xf9\x94\x68\xe8\x2e\x8f\x61\x2e\x5d\xbb\xe0\x9f\xc9\x1a\x77\x92\x28\xf0\x41\xcc\x77\xcf\xc2\x2b\xb\x44\xf7\xd6\x55\x8\xc3\x1d\x15\xb6\x27\x9e\xd3\x4f\xa5\xcc\xc5\xeb\x1c\xe4\x5e\x4a\xd2\x7d\x3d\x7f\x9c\x3a\x9f\x5e\x8\xf6";
?>