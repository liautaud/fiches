var options = {
    edges: {
        smooth: {
            type: 'cubicBezier',
            forceDirection: 'vertical',
            roundness: 0.3
        }
    },
    
    layout: {
        hierarchical: {
            direction: 'UD',
            sortMethod: 'directed',
            levelSeparation: 80,
            nodeSpacing: 200
        }
    },

    interaction: {
        dragNodes: false
    },

    physics: {
        enabled: false 
    }
};

var colors = {
    'grey':   { background:'lightgrey', border:'grey' },
    'yellow': { background:'#ffc107',   border:'grey' },
    'blue':   { background:'#00bcd4',   border:'grey' },
    'green':  { background:'#8bc34a',   border:'grey' },
    'red':    { background:'#f44336',   border:'grey' },
    'zero':   { background:'white',     border:'white' }
};

var getChildren = function (edges, node) {
    var list = [];

    edges.forEach(function (edge) {
        if (edge.from == node) {
            list.push(edge.to);
        }
    });

    return list;
};

var getParents = function (edges, node) {   
    var list = [];

    edges.forEach(function (edge) {
        if (edge.to == node) {
            list.push(edge.from);
        }
    });

    return list;
};

var firstAvailable = function (nodes, waiting) {
    var first;

    waiting.forEach(function (item, index) {
        if (typeof first != 'undefined') {
            return;
        }

        var node = item.node;
        var parents = item.parents;

        if (parents.every(function (parent) {
            return (typeof nodes[parent].level != 'undefined');
        })) {
            first = index;
        }
    });

    return first;
};

var setLevels = function (edges, nodes) {
    nodes[0].level = 0;

    // On maintient une liste des noeuds en attente de traitement.
    // En pratique, les seuls noeuds susceptibles d'y rester plus
    // d'une itération sont ceux qui ont plusieurs parents, mais
    // dont au moins un des parents ne s'est pas encore vu
    // attribuer de niveau.
    var waiting = getChildren(edges, 0).map(function (node) {
        return {node: node, parents: getParents(edges, node)};
    });

    while (waiting.length > 0) {
        var index = firstAvailable(nodes, waiting);
        var item = waiting[index];
        var node = item.node;
        var parents = item.parents;

        // Le niveau d'un noeud est 1 + max(Niveaux des parents).
        var maxLevel = 0;
        parents.forEach(function (parent) {
            if (nodes[parent].level > maxLevel) {
                maxLevel = nodes[parent].level;
            }
        });
        nodes[node].level = maxLevel + 1;

        // On ajoute les fils du noeud à la liste des noeuds
        // en attente de traitement.
        Array.prototype.push.apply(
            waiting, 
            getChildren(edges, node).map(function (child) {
                return {node: child, parents: getParents(edges, child)};
            })
        );

        // On retire finalement le noeud traité de la liste.
        waiting.splice(index, 1);
    }
};

var processNodes = function (data) {
    Object.keys(data.nodes).map(function (id, index) {
        data.nodes[id].color = colors[data.nodes[id].color];
    });

    setLevels(data.edges, data.nodes);

    var nodes = [];

    Object.keys(data.nodes).map(function (id, index) {
        nodes.push(data.nodes[id]);
    });

    return nodes;
};

var processEdges = function (data) {
    return data.edges;
};

var Graph = function (container, data, admin) {
    this.container = container;
    this.data = data;
    this.edges = processEdges(data);
    this.nodes = processNodes(data);
    this.instance = null;
    this.admin = admin;
};

Graph.prototype.draw = function () {
    var self = this;
    this.destroy();

    this.instance = new vis.Network(this.container, { nodes: this.nodes, edges: this.edges }, options);
    this.instance.moveTo({offset: {x: -100, y: -50}});

    this.instance.on('doubleClick', function (params) {
       if (params.nodes.length > 0) {
           var id = params.nodes[0];
           var node = self.data.nodes[id];

           if (!self.admin && (node.color == colors['grey'] || node.color == colors['zero'])) {
               return false;
           }

           window.location.href = node.url;
       }
    });
};

Graph.prototype.destroy = function () {
    if (this.instance == null) {
        return;
    }

    this.instance.destroy();
    this.instance = null;
};

window.Graph = Graph;
