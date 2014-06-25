<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendMediaBundle\Field;

/**
 * Image field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ImageField extends AbstractField
{
    const TYPE = 'image';
    public $options = false;
    public $icon = 'p-frontendmedia-field_image-icon';

    /**
     * Transform item values
     *
     * @param array $item
     * @param array $media
     * @param array $options
     *
     * @return array
     */
    protected function _transform(array $item, array $media, array $options)
    {
        $item['templates']        = array();
        $item['templates_config'] = array();
        $item['media']            = array();

        try
        {
            if (!empty($item['data_content']))
            {
                $file = $this->_getFile($item['data_content']);

                if ($file !== null)
                {
                    $item['media']  = $this->_getMediaData($file);
                    $item['master'] = $this->_getMasterData($item);

                    $attributes              = $file->getAsset()->getAttributes();
                    $item['media']['width']  = $attributes->width;
                    $item['media']['height'] = $attributes->height;

                    try
                    {
                        $item['templates'] = array_merge($item['templates'], $this->_getImageTemplates($item, $media, $file));
                    }
                    catch(Exception $e)
                    {
                        MWF_Log::exception($e);
                    }

                    try
                    {
                        $item['templates_config'] = array_merge($item['templates_config'], $this->_getTemplateConfig($media, $file));
                    }
                    catch(Exception $e)
                    {
                        MWF_Log::exception($e);
                    }
                }
            }

        }
        catch (Exception $e)
        {
            MWF_Log::exception($e);
            $item['data_content'] = '';
            $item['media'] = false;
        }

        return $item;
    }
}
