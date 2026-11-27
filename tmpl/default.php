<?php
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
$slides = (array) $params->get('slides', []);
if (!$slides) return;
$doc = Factory::getApplication()->getDocument();
$doc->addStyleSheet(Uri::root(true) . '/media/mod_venecia_slider/slider.css?v=1.0.17');
$doc->addScript(Uri::root(true) . '/media/mod_venecia_slider/slider.js?v=1.0.8', [], ['defer' => true]);
$esc = static fn ($s) => htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
$hexColor = static function ($value, string $fallback): string {
    return is_string($value) && preg_match('/^#[0-9a-f]{6}$/iD', $value) ? $value : $fallback;
};
$defaultTitleColor = $hexColor($params->get('title_color'), '#102246');
$defaultSubtitleColor = $hexColor($params->get('subtitle_color'), '#102246');
$validUrl = static function ($url) { $url = trim((string) $url); return $url !== '' && !preg_match('/[\x00-\x20]/', $url) && (!preg_match('/^([a-z][a-z0-9+.-]*):/i', $url, $m) || in_array(strtolower($m[1]), ['http', 'https', 'tel', 'mailto'], true)); };
?>
<section class="venecia-slider" aria-label="<?php echo $esc(Text::_('MOD_VENECIA_SLIDER_LABEL')); ?>" data-interval="<?php echo (int) $params->get('autoplay', 1) ? max(2, (int) $params->get('interval', 8)) * 1000 : 0; ?>">
 <div class="venecia-slider__track">
 <?php foreach ($slides as $i => $slide) :
     $slide = (object) $slide;
     $color = $hexColor($slide->background ?? null, '#e8f4f2');
     $titleColor = $hexColor($slide->title_color ?? null, $defaultTitleColor);
     $subtitleColor = $hexColor($slide->subtitle_color ?? null, $defaultSubtitleColor);
     $image = !empty($slide->image) ? HTMLHelper::cleanImageURL($slide->image)->url : '';
     $subtitle = trim((string) ($slide->subtitle ?? ''));
     $lines = preg_split('/\R+/u', $subtitle) ?: [];
     $items = preg_grep('/^\h*•\h+\S/u', $lines);
     $isChecklist = count($lines) > 1 && count($items) === count($lines);
     $slideClass = $image === '' ? '' : ' venecia-slider__slide--photo';
     $slideStyle = "--venecia-slide-bg:$color;--venecia-title-color:$titleColor;--venecia-subtitle-color:$subtitleColor";
     if ((int) ($slide->text_shade ?? 1) === 1) {
         $slideClass .= ' venecia-slider__slide--shade';
         $shade = $slide->text_shade_color ?? null;
         $shadeColor = $hexColor($shade, $shade === 'dark' ? '#102246' : '#ffffff');
         $transparency = max(0, min(100, (int) ($slide->text_shade_transparency ?? 12)));
         $shadeOpacity = (100 - $transparency) . '%';
         $slideStyle .= ";--venecia-shade-color:$shadeColor;--venecia-shade-opacity:$shadeOpacity";
     }
     if ($isChecklist) {
         $slideClass .= ' venecia-slider__slide--checklist';
     }
 ?>
 <div class="venecia-slider__slide<?php echo $slideClass; ?>" style="<?php echo $esc($slideStyle); ?>" <?php echo $i === array_key_first($slides) ? 'data-active' : 'hidden inert'; ?>>
  <div class="container">
   <div class="row align-items-center justify-content-center">
    <div class="col-12 col-lg-10 col-xxl-11 text-center venecia-slider__copy">
     <?php if (!empty($slide->title)) : ?>
      <h2 class="venecia-slider__title"><?php echo $esc($slide->title); ?></h2>
     <?php endif; ?>
     <?php if ($isChecklist) : ?>
      <ul class="venecia-slider__checklist row row-cols-1 row-cols-md-2 gy-2 gx-md-4 list-unstyled mx-auto mb-0 text-start" role="list">
       <?php foreach ($items as $item) : ?>
        <li class="col d-flex align-items-start gap-2">
         <svg class="venecia-slider__checkmark flex-shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6"/></svg>
         <span><?php echo $esc(preg_replace('/^\h*•\h+/u', '', $item)); ?></span>
        </li>
       <?php endforeach; ?>
      </ul>
     <?php elseif ($subtitle !== '') : ?>
      <p class="venecia-slider__subtitle<?php echo str_contains($subtitle, "\n") ? ' venecia-slider__subtitle--multiline' : ''; ?>"><?php echo nl2br($esc($subtitle)); ?></p>
     <?php endif; ?>
     <div class="d-flex flex-wrap justify-content-center gap-3">
      <?php foreach ([1, 2] as $number) :
          $prefix = 'button' . $number . '_';
          $text = trim((string) ($slide->{$prefix . 'text'} ?? ''));
          $url = trim((string) ($slide->{$prefix . 'url'} ?? ''));
          if ($text === '' || !$validUrl($url)) {
              continue;
          }
          $class = $slide->{$prefix . 'class'} ?? ($number === 1 ? 'knopka' : 'knopka2');
          $icon = trim((string) ($slide->{$prefix . 'icon'} ?? ''));
          $newTab = ($slide->{$prefix . 'target'} ?? '') === 'new';
      ?>
       <a class="<?php echo $esc($class); ?>" href="<?php echo $esc($url); ?>"<?php echo $newTab ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
        <?php echo $esc($text); ?>
        <?php if ($icon !== '') : ?><span class="<?php echo $esc($icon); ?>" aria-hidden="true"></span><?php endif; ?>
       </a>
      <?php endforeach; ?>
     </div>
    </div>
   </div>
  </div>
  <?php if ($image !== '') : ?>
   <img class="venecia-slider__image" src="<?php echo $esc($image); ?>" alt="" <?php echo $i === array_key_first($slides) ? 'fetchpriority="high"' : 'loading="lazy"'; ?> decoding="async">
  <?php endif; ?>
 </div><?php endforeach; ?>
 </div>
 <?php if (count($slides) > 1) : ?>
  <div class="venecia-slider__controls" hidden>
   <button class="venecia-slider__arrow" type="button" data-prev aria-label="<?php echo $esc(Text::_('MOD_VENECIA_SLIDER_PREV')); ?>">
    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12h15m-6-6 6 6-6 6"/></svg>
   </button>
   <button class="venecia-slider__arrow" type="button" data-next aria-label="<?php echo $esc(Text::_('MOD_VENECIA_SLIDER_NEXT')); ?>">
    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12h15m-6-6 6 6-6 6"/></svg>
   </button>
   <div class="venecia-slider__dots">
    <?php for ($index = 0, $total = count($slides); $index < $total; $index++) : ?>
     <button type="button" data-dot="<?php echo $index; ?>" aria-label="<?php echo $esc(Text::sprintf('MOD_VENECIA_SLIDER_GO_TO', $index + 1)); ?>"<?php echo $index === 0 ? ' aria-current="true"' : ''; ?>></button>
    <?php endfor; ?>
   </div>
  </div>
 <?php endif; ?>
</section>
