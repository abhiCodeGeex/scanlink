<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

/**
 * Legacy SectionTitle help icons + "Video Tutorial" hover tooltips
 * (Kohana location/edit.php + styles/style.css .help-div).
 */
class LegacySectionHelp
{
    /**
     * YouTube embed URLs keyed by section title (legacy location form).
     *
     * @var array<string, string>
     */
    public const VIDEOS = [
        'Logo' => 'https://www.youtube.com/embed/hGrBYsys2Oo?rel=0',
        'Videos' => 'https://www.youtube.com/embed/H33caspIlcc?rel=0',
        'Words' => 'https://www.youtube.com/embed/CZx8xplEfoU?rel=0',
        'Pictures' => 'https://www.youtube.com/embed/GshHCp9F0wU?rel=0',
        'Documents' => 'https://www.youtube.com/embed/ujiEr65yg30?rel=0',
        'Web Link' => 'https://www.youtube.com/embed/id0I8j8RTuY?rel=0',
        'Data Collection' => 'https://www.youtube.com/embed/C_vH14MFtXA?rel=0',
        'Set Code Type' => 'https://www.youtube.com/embed/jCeyQOfm7uc?rel=0',
        'Header' => 'https://www.youtube.com/embed/eJtzHbZoCPw?rel=0',
        'User Access Security' => 'https://www.youtube.com/embed/KcXJnxuMVyc?rel=0',
        'Share' => 'https://www.youtube.com/embed/qOi6tSBsII4?rel=0',
        'Form Builder' => 'https://www.youtube.com/embed/cYQnzxkp528?rel=0',
    ];

    public static function heading(string $title, ?string $videoUrl = null): HtmlString|string
    {
        $videoUrl ??= self::VIDEOS[$title] ?? null;

        if ($videoUrl === null || $videoUrl === '') {
            return $title;
        }

        $icon = e(asset('images/help_icon.jpg'));
        $href = e($videoUrl);
        $label = e($title);

        return new HtmlString(
            '<span class="sl-section-title-text">'.$label.'</span>'
            .'<span class="sl-help-wrap">'
            .'<img src="'.$icon.'" class="help-icon" alt="Help" width="18" height="18">'
            .'<span class="help-div" role="tooltip">'
            .'<a href="'.$href.'" class="sl-help-video" data-video="'.$href.'">Video Tutorial</a>'
            .'<span class="arrow-bg" aria-hidden="true"></span>'
            .'</span>'
            .'</span>'
        );
    }
}
