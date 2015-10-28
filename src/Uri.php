<?php

namespace JVal;

class Uri
{
    private $raw;
    private $parts;
    private $scheme;
    private $authority;
    private $path;
    private $query;
    private $segments;
    private $primaryIdentifier;

    public function __construct($rawUri)
    {
        $this->buildFromRawUri($rawUri);
    }

    public function getRawUri()
    {
        return $this->raw;
    }

    public function getRawPointer()
    {
        return isset($this->parts['fragment']) ? $this->parts['fragment'] : '';
    }

    public function isAbsolute()
    {
        return $this->scheme !== '';
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    public function getAuthority()
    {
        return $this->authority;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getPointerSegments()
    {
        return $this->segments;
    }

    public function getPrimaryResourceIdentifier()
    {
        return $this->primaryIdentifier;
    }

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
