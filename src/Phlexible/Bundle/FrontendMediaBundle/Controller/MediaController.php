<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendMediaBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Media controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/media")
 */
class MediaController extends Controller
{
    /**
     * Deliver a media asset
     *
     * @param string $fileId
     * @param string $template
     *
     * @return Response
     * @Route("/thumbnail/{fileId}/{template}", name="frontendmedia_thumbnail")
     */
    public function thumbnailAction($fileId, $template)
    {
        $templateKey = str_replace('.jpg', '', $template);

        $siteManager = $this->get('phlexible_media_site.site_manager');
        $templateManager = $this->get('phlexible_media_template.template_manager');

        $site = $siteManager->getByFileId($fileId);
        $file = $site->findFile($fileId);
        $template = $templateManager->find($templateKey);

        $outfile = $this->container->getParameter('app.web_dir') . '/media/' . $fileId . '/' . $templateKey . '.jpg';
        if (!file_exists($outfile)) {
            if (!file_exists(dirname($outfile))) {
                mkdir(dirname($outfile), 0777, true);
            }
            $this->get('phlexible_media_template.applier.image')->apply($template, $file, $file->getPhysicalPath(), $outfile);
        }

        return $this->get('igorw_file_serve.response_factory')
            ->create($outfile, 'image/jpeg', ['absolute_path' => true]);
    }

    /**
     * Download a media file
     *
     * @param string $fileId
     *
     * @return Response
     * @Route("/download/{fileId}", name="frontendmedia_download")
     */
    public function downloadAction($fileId)
    {
        $siteManager = $this->get('phlexible_media_site.site_manager');

        $site = $siteManager->getByFileId($fileId);
        $file = $site->findFile($fileId);

        $filePath = $file->getPhysicalPath();
        $mimeType = $file->getMimeType();

        return $this->get('igorw_file_serve.response_factory')
            ->create($filePath, $mimeType, ['absolute_path' => true, 'inline' => false]);
    }

    /**
     * Deliver a media asset
     *
     * @param string $fileId
     *
     * @return Response
     * @Route("/inline/{fileId}", name="frontendmedia_inline")
     */
    public function inlineAction($fileId)
    {
        $siteManager = $this->get('phlexible_media_site.site_manager');

        $site = $siteManager->getByFileId($fileId);
        $file = $site->findFile($fileId);

        $filePath = $file->getPhysicalPath();
        $mimeType = $file->getMimeType();

        return $this->get('igorw_file_serve.response_factory')
            ->create($filePath, $mimeType, ['absolute_path' => true, 'inline' => true]);
    }

    /**
     * @param string $fileId
     * @param int    $size
     *
     * @return Response
     * @Route("/icon/{fileId}/{size}", name="frontendmedia_icon")
     */
    public function iconAction($fileId, $size = 16)
    {
        $siteManager = $this->get('phlexible_media_site.site_manager');
        $documenttypeManager = $this->get('phlexible_documenttype.documenttype_manager');

        $site = $siteManager->getByFileId($fileId);
        $file = $site->findFile($fileId);
        $mimeType = $file->getMimeType();

        $documenttype = $documenttypeManager->findByMimetype($mimeType);
        $icon = $documenttype->getIcon($size);

        return $this->get('igorw_file_serve.response_factory')
            ->create($icon, 'image/gif', ['absolute_path' => true]);
    }
}
