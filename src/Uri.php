<?php

namespace JVal;

/**
 * Wraps a raw URI string, providing methods to deal with URI normalization,
 * comparison and resolution (including JSON pointers references).
 */
class Uri
{
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
    private $scheme;

    /**
     * @var string
     */
    private $authority;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $query;

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
        return isset($this->parts['fragment']) ? $this->parts['fragment'] : '';
    }

    /**
     * @return bool
     */
    public function isAbsolute()
    {
        return $this->scheme !== '';
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
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
        return $this->path;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return string[]
     */
    public function getPointerSegments()
    {
        return $this->segments;
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
     * Example:
     *
     * Current  = foo.json
     * Other    = http://localhost/bar/baz
     * Resolved = http://localhost/bar/foo.json
     *
     * @param Uri $uri
     * @return string
     */
    public function resolveAgainst(Uri $uri)
    {
        if ($this->isAbsolute()) {
            throw new \LogicException(
                'Cannot resolve against another URI: URI is already absolute'
            );
        }

        if (!$uri->isAbsolute()) {
            throw new \LogicException(
                'Cannot resolve against another URI: reference URI is not absolute'
            );
        }

        $scheme = $uri->getScheme();
        $authority = $uri->getAuthority();
        $path = $uri->getPath();
        $query = $uri->getQuery();

        if ($this->getAuthority()) {
            $authority = $this->getAuthority();
            $path = $this->getPath();
            $query = $this->getQuery();
        } elseif ($this->getPath()) {
            $ownPath = $this->getPath();
            $againstPath = $uri->getPath();
            $query = $this->getQuery();

            if (0 !== strpos($ownPath, '/')) {
                $againstPath = $againstPath ?: '/';
                $path = preg_replace('#/([^/]*)$#', "/{$ownPath}", $againstPath);
            } else {
                $path = $ownPath;
            }
        } elseif ($this->getQuery()) {
            $query = $this->getQuery();
        }

        $fragment = isset($this->parts['fragment']) ? $this->parts['fragment'] : '';
        $resolved = "{$scheme}://{$authority}{$path}";

        if ($query) {
            $resolved .= '?' . $query;
        }

        if ($fragment) {
            $resolved .= '#' . $fragment;
        }

        $this->buildFromRawUri($resolved);

        return $resolved;
    }

    /**
     * Returns whether two URIs share the same primary resource identifier,
     * i.e. whether they point to the same document.
     *
     * @param Uri $uri
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

        $this->scheme = isset($this->parts['scheme']) ? $this->parts['scheme'] : '';
        $this->path = isset($this->parts['path']) ? $this->parts['path'] : '';
        $this->query = isset($this->parts['query']) ? $this->parts['query'] : '';
        $this->authority = $this->buildAuthority();
        $this->segments = $this->buildSegments();
        $this->primaryIdentifier = $this->buildPrimaryIdentifier();
    }

    private function buildAuthority()
    {
        $authority = '';
        $userInfo = '';

        if (isset($this->parts['user'])) {
            $userInfo.= $this->parts['user'];
        }

        if (isset($this->parts['pass'])) {
            $userInfo .= ':' . $this->parts['pass'];
        }

        if ($userInfo !== '') {
            $authority .= $userInfo . '@';
        }

        if (isset($this->parts['host'])) {
            $authority .= $this->parts['host'];
        }

        if (isset($this->parts['port'])) {
            $authority .= ':' . $this->parts['port'];
        }

        return $authority;
    }

    private function buildSegments()
    {
        $segments = [];

        if (isset($this->parts['fragment'])) {
            $rawSegments = explode('/', $this->parts['fragment']);

            foreach ($rawSegments as $segment) {
                $segment = trim($segment);

                if ($segment !== '') {
                    $segment = str_replace('~1', '/', $segment);
                    $segment = str_replace('~0', '~', $segment);
                    $segments[] = $segment;
                }
            }
        }

        return $segments;
    }

    private function buildPrimaryIdentifier()
    {
        $identifier = '';

        if ($this->scheme) {
            $identifier .= $this->scheme . '://';
        }

        $identifier .= $this->authority . $this->path;

        if ($this->query) {
            $identifier .= '?' . $this->query;
        }

        return $identifier;
    }
}
