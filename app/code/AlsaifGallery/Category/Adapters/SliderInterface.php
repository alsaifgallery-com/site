<?php
namespace AlsaifGallery\Category\Adapters;

interface SliderInterface {
    
    public function getSliders();
    public function getSlider($sliderId);
    public function getSliderBannerCollection($sliderId);
    
}
