<?php

/*
 * This file is part of the JVal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JVal;

/**
 * Wraps a raw URI string, providing methods to deal with URI normalization,
 * comparison and resolution (including JSON pointers references).
 */
class Uri
{
    private static $partNames = [
        'scheme',
        'user',
        'pass',
        'host',
        'port',
        'path',
        'query',
        'fragment',
    ];

    /**
     * @var string
     */
    private $raw;

    /**
     * @var array
     */
    private $parts;

    /**
     * @var string
     */
    private $authority;

    /**
     * @var string[]
     */
    private $segments;

    /**
     * @var string
     */
    private $primaryIdentifier;

    /**
     * Constructor.
     *
     * @param string $rawUri
     */
    public function __construct($rawUri)
    {
        $this->buildFromRawUri($rawUri);
    }

    /**
     * @return string
     */
    public function getRawUri()
    {
        return $this->raw;
    }

    /**
     * @return string
     */
    public function getRawPointer()
    {
        return $this->parts['fragment'];
    }

    /**
     * @return bool
     */
    public function isAbsolute()
    {
        return $this->parts['scheme'] !== '';
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->parts['scheme'];
    }

    /**
     * @return string
     */
    public function getAuthority()
    {
        return $this->authority;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->parts['path'];
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->parts['query'];
    }

    /**
     * @return string[]
     */
    public function getPointerSegments()
    {
        return $this->segments;
    }

    /**
     * @return bool
     */
    public function hasPointer()
    {
        return !empty($this->segments);
    }

    /**
     * Returns the primary resource identifier part of the URI, i.e. everything
     * excluding its fragment part.
     *
     * @return string
     */
    public function getPrimaryResourceIdentifier()
    {
        return $this->primaryIdentifier;
    }

    /**
     * Resolves the current (relative) URI against another (absolute) URI.
     * Example:.
     *
     * Current  = foo.json
     * Other    = http://localhost/bar/baz
     * Resolved = http://localhost/bar/foo.json
     *
     * @param Uri $uri
     *
     * @return Uri
     */
    public function resolveAgainst(Uri $uri)
    {
        if ($this->isAbsolute()) {
            return $this;
        } elseif (!$uri->isAbsolute()) {
            throw new \LogicException(
                'Cannot resolve against another URI: reference URI is not absolute'
            );
        } else {
            $resolvedUri = $this->buildResolvedUriAgainst($uri);
            return new self($resolvedUri);
        }
    }

    /**
     * Returns whether two URIs share the same primary resource identifier,
     * i.e. whether they point to the same document.
     *
     * @param Uri $uri
     *
     * @return bool
     */
    public function isSamePrimaryResource(Uri $uri)
    {
        if (!$this->isAbsolute() || !$uri->isAbsolute()) {
            throw new \LogicException('Cannot compare URIs: both must be absolute');
        }

        return $this->primaryIdentifier === $uri->getPrimaryResourceIdentifier();
    }

    private function buildFromRawUri($rawUri)
    {
        $this->raw = rawurldecode($rawUri);
        $this->parts = @parse_url($this->raw);

        if (false === $this->parts) {
            throw new \InvalidArgumentException("Cannot parse URI '{$rawUri}'");
        }

        foreach (self::$partNames as $part) {
            if (!isset($this->parts[$part])) {
                $this->parts[$part] = '';
            }
        }

        if ($this->parts['scheme'] === 'file' && preg_match('/^[A-Z]:/i', $this->parts['path'])) {
            $this->parts['path'] = '/' . $this->parts['path'];
        }

        $this->authority = $this->buildAuthority();
        $this->segments = $this->buildSegments();
        $this->primaryIdentifier = $this->buildPrimaryIdentifier();
    }

    private function buildAuthority()
    {
        $userInfo = $this->parts['user'];
        $authority = $this->parts['host'];

        if ($this->parts['pass'] !== '') {
            $userInfo .= ':'.$this->parts['pass'];
        }

        if ($this->parts['port'] !== '') {
            $authority .= ':'.$this->parts['port'];
        }

        if ($userInfo !== '') {
            $authority = $userInfo.'@'.$authority;
        }

        return $authority;
    }

    private function buildSegments()
    {
        $segments = [];

        if (substr($this->parts['fragment'], 0, 1) === '/') {
            $rawSegments = explode('/', substr($this->parts['fragment'], 1));

            foreach ($rawSegments as $segment) {
                $segment = str_replace('~1', '/', $segment);
                $segment = str_replace('~0', '~', $segment);
                $segments[] = $segment;
            }
        }

        return $segments;
    }

    private function buildPrimaryIdentifier()
    {
        $identifier = '';

        if ($this->parts['scheme'] !== '') {
            $identifier .= $this->parts['scheme'].'://';
        }

        $identifier .= $this->authority.$this->parts['path'];

        if ($this->parts['query'] !== '') {
            $identifier .= '?'.$this->parts['query'];
        }

        if ($this->parts['fragment'] !== '' && $this->parts['fragment'][0] !== '/') {
            $identifier .= '#'.$this->parts['fragment'];
        }

        return $identifier;
    }

    private function buildResolvedUriAgainst(Uri $uri)
    {
        $scheme = $uri->getScheme();
        $authority = $uri->getAuthority();
        $path = $uri->getPath();
        $query = $uri->getQuery();

        if ($this->getAuthority()) {
            $authority = $this->getAuthority();
            $path = $this->getPath();
            $query = $this->getQuery();
        } elseif ($this->getPath()) {
            $path = $this->buildResolvedPathAgainst($uri->getPath());
            $query = $this->getQuery();
        } elseif ($this->getQuery()) {
            $query = $this->getQuery();
        }

        return $this->appendRelativeParts(
            "{$scheme}://{$authority}{$path}",
            $query,
            $this->parts['fragment']
        );
    }

    private function buildResolvedPathAgainst($againstPath)
    {
        $ownPath = $this->getPath();

        if (0 !== strpos($ownPath, '/')) {
            $againstPath = $againstPath ?: '/';

            return preg_replace('#/([^/]*)$#', "/{$ownPath}", $againstPath);
        }

        return $ownPath;
    }

    private function appendRelativeParts($absolutePart, $query, $fragment)
    {
        if ($query) {
            $absolutePart .= '?'.$query;
        }

        if ($fragment) {
            $absolutePart .= '#'.$fragment;
        }

        return $absolutePart;
    }
}
