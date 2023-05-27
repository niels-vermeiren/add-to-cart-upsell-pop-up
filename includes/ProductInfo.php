<?php

class ProductInfo
{
    private $_name;
    private $_price;
    private $_product_id;
    private $_upsells;
    private $_crossells;
    private $_thumbnail;
    private $_post_meta;
    private $_product_type;
    private $_attributes;
    private $_tags;
    private $_categories;
    private $_related;
    private $_stock;

    public function __construct($product_id)
    {
        $this->_product_id = $product_id;
    }

    public function build()
    {
        global $woocommerce;
        $prodObj = wc_get_product($this->_product_id);

        if (!$prodObj) {
            throw ProductNotFoundException("Product with id not found");
        }

        $this->set_name($prodObj->get_name());
        $this->set_price($prodObj->get_price());
        $this->set_upsells($prodObj->get_upsells());
        $this->set_crossells($prodObj->get_cross_sells());

        $imageObj = wp_get_attachment_image_src(get_post_thumbnail_id($upsell), 'single-post-thumbnail');
        if($imageObj) {
            $this->set_thumbnail($imageObj[0]);
        }
        $this->set_productType($prodObj->get_type());
        $this->set_postMeta($prodObj->get_meta_data());
        $this->set_attributes($prodObj->get_attributes());
        $this->set_tags($prodObj->get_tags());
        $this->set_related($prodObj->get_related());
        $this->set_stock($prodObj->get_total_stock());
    }

    public function toDTO()
    {
        return json_decode(json_encode($this), true);
    }

    public function get_name()
    {
        return $this->_name;
    }
    public function set_name($_name): self
    {
        $this->_name = $_name;
        return $this;
    }

    public function get_price()
    {
        return $this->_price;
    }
    public function set_price($_price): self
    {
        $this->_price = $_price;
        return $this;
    }

    public function get_productId()
    {
        return $this->_product_id;
    }
    public function set_productId($_product_id): self
    {
        $this->_product_id = $_product_id;
        return $this;
    }

    public function get_upsells()
    {
        return $this->_upsells;
    }
    public function set_upsells($_upsells): self
    {
        $this->_upsells = $_upsells;
        return $this;
    }

    public function get_crossells()
    {
        return $this->_crossells;
    }
    public function set_crossells($_crossells): self
    {
        $this->_crossells = $_crossells;
        return $this;
    }

    public function get_thumbnail()
    {
        return $this->_thumbnail;
    }
    public function set_thumbnail($_thumbnail): self
    {
        $this->_thumbnail = $_thumbnail;
        return $this;
    }

    public function get_postMeta()
    {
        return $this->_post_meta;
    }
    public function set_postMeta($_post_meta): self
    {
        $this->_post_meta = $_post_meta;
        return $this;
    }

    public function get_productType()
    {
        return $this->_product_type;
    }
    public function set_productType($_product_type): self
    {
        $this->_product_type = $_product_type;
        return $this;
    }



    public function get_attributes()
    {
        return $this->_attributes;
    }
    public function set_attributes($_attributes): self
    {
        $this->_attributes = $_attributes;
        return $this;
    }

    public function get_tags()
    {
        return $this->_tags;
    }
    public function set_tags($_tags): self
    {
        $this->_tags = $_tags;
        return $this;
    }

    public function get_categories()
    {
        return $this->_categories;
    }
    public function set_categories($_categories): self
    {
        $this->_categories = $_categories;
        return $this;
    }

    public function get_related()
    {
        return $this->_related;
    }
    public function set_related($_related): self
    {
        $this->_related = $_related;
        return $this;
    }

    public function get_stock()
    {
        return $this->_stock;
    }
    public function set_stock($_stock): self
    {
        $this->_stock = $_stock;
        return $this;
    }
}
