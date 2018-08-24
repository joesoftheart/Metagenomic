args <- commandArgs(TRUE)
library(gplots)
library(vegan)
library(RColorBrewer)
all.data = read.csv(args[1], header = T, check.names = FALSE)
dim(all.data)
all.data[1 : 3, 1 : 4]
row.names(all.data) = all.data$taxon
all.data = all.data[, - 1]
data.prop <- all.data / rowSums(all.data)
data.prop[1 : 3, 1 : 3]
col_breaks = unique(c(seq(0, 0.04, length = 100), seq(0.04, 0.08, length = 100), seq(0.08, 0.12, length = 100), seq(0.12, 0.16, length = 100), seq(0.16, 0.20, length = 100), seq(0.20, 0.24, length = 100)))
scaleyellowred <- colorRampPalette(c("snow1", "yellow", "green3", "blue", "orange", "red"), space = "rgb")(length(col_breaks) - 1)

maxab <- apply(data.prop, 2, max)
head(maxab)

# remove the genera with less than 1% as their maximum relative abundance (in this case less than 0.01)
n1 <- names(which(maxab < 0.01))
n1
data.prop.1 <- data.prop[, - which(names(data.prop) %in% n1)]

#png(args[2], width = 15, height = 13, units = "in", res = 300)
svg(args[2],width=15,height=13)
#No dendrogram and ordered label will the same in the original file
heatmap.2(as.matrix(t(data.prop.1)), dendrogram = 'none', Rowv = FALSE,
Colv = FALSE, col = scaleyellowred, breaks = col_breaks,
#margins = c(12, 10), #c(11,5),
margins = c(10,10),
#xlab = "", ylab = "", srtCol = 45, cexRow = 1.0,
xlab = "", ylab = "", srtCol = 45, cexRow = 1.1,
cexCol = 1.8, trace = "none", density.info = "none",
key = TRUE, symkey = FALSE, key.xlab = "Relative abundance",
#keysize = 1.0, key.par = list(mar = c(5, 0, 10, 25)), #(mar=c(5,0,6.2,25)), #key.par = list(cex=1.5),
keysize = 1.0, key.par = list(mar = c(9, 0, 12, 9)), 
#lmat = rbind(c(5, 4, 2), c(6, 1, 3)), lhei = c(2, 5), lwid = c(2, 10, 2)) #key.par=list(mar=c(3.5,0,3,3)
lmat = rbind(c(5, 4, 2), c(6, 1, 3)), lhei = c(2, 5), lwid = c(2, 4, 2)) 
dev.off()


