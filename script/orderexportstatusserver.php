<?php

use Magento\Framework\App\Bootstrap;

require __DIR__ . '/app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$obj = $bootstrap->getObjectManager();

// Set the state (not sure if this is neccessary)
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');
$obj->get('Magento\Framework\Registry')->register('isSecureArea', true);

$orderFactory = $obj->create('Magento\Sales\Model\ResourceModel\Order\CollectionFactory');
$directoryList = $obj->get('Magento\Framework\Filesystem\DirectoryList');
$fileDriver = $obj->get('Magento\Framework\Filesystem\Driver\File');
$csvProcessor = $obj->get('Magento\Framework\File\Csv');
$orderRepository = $obj->get('Magento\Sales\Api\OrderRepositoryInterface');
$ioAdapter = $obj->get('Magento\Framework\Filesystem\Io\File');
$sftpConnection = $obj->get('Magento\Framework\Filesystem\Io\Sftp');

$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/aws-server-order-cron.log');
$logger = new \Zend\Log\Logger();
$logger->addWriter($writer);

$orderFactory = $orderFactory->create();
foreach($orderFactory as $order){
    $incrementId = $order->getIncrementId();
    $logger->info($incrementId);
    $fileName = $incrementId."_order.txt";
    $erpFolder = 'application/erp/import';
    $completedFolder = 'application/erp/completed';

    $sftp = loginSftp();
    $baseDirectory = $sftp->pwd();
    $sftp->cd($erpFolder);
    $filePath =  $sftp->pwd() ."/". $fileName;
    $sftp->cd($filePath);
    $filesName = $sftp->ls();
    $csvName='';
    $csvPath='';
    foreach($filesName as $file){
        foreach ($file as $key => $value) {
            if($value == $fileName){
                $csvPath=$file['id'];
                $csvName=$file['text'];
                break;
            }
        }
    }  

    $filepath = $csvPath;

    if($filepath !== '')
    {
        try
        {
            $fileRead = $sftp->read($filepath);
            $dataArray = explode(',',$fileRead);
            $number = explode('MSG',$dataArray[6]);
        
            $erpOrderNumber = $number[1];
            $orderStatus = $dataArray[8];
            $message = $dataArray[12];

            $order = $orderRepository->get($order->getEntityId());
            if($orderStatus == "Failed" || $orderStatus == "FAILED" || $orderStatus == "failed"){
                $order->setErpOrderNumber($erpOrderNumber);
                $order->setAcknowleadge('ERROR');
                $logger->info($orderStatus);
            }else{
                $order->setErpOrderNumber($erpOrderNumber);
                $order->setAcknowleadge('SUCCESS');
            }
            $order->save();
            $completedFolderDir = $baseDirectory."/".$completedFolder;
            $completedFolderDirFile = $completedFolderDir."/".$fileName;
            if (!is_dir($completedFolderDir))
            {   
                $sftp->mkdir($completedFolderDir, 0777, false);
            }

            $sftp->rm($completedFolderDirFile);
           
            $sftp->mv($filepath, $completedFolderDirFile);
            echo __("SFTP file read successfully"."\n");
        }
        catch (Exception $e)
        {
            echo __("Inserted Fail"."\n");
        }
    }
}

function loginSftp()
{
	$bootstrap = Bootstrap::create(BP, $_SERVER);

	$obj = $bootstrap->getObjectManager();
	$state = $obj->get('Magento\Framework\App\State');
	$state->setAreaCode('frontend');
	$obj->get('Magento\Framework\Registry')->register('isSecureArea', true);
	$sftpConnection = $obj->get('Magento\Framework\Filesystem\Io\Sftp');
    // $host = '216.14.118.191';
    // $port = '2223';
    // $username = '4sgm';
    // $password = 'w#gVC8U9fg5s$J2#';
    
    $host = '100.20.224.196';
    $port = '22';
    $username = '4sgm';
    $password = 'yrS8&bPlt#Ow4ks';

    $sftpConnection->open(
        array(
            'host' => $host.':'.$port,
            'username' => $username,
            'password' => $password
        )
    );

    return $sftpConnection;
}
echo "Done";
exit;
?>